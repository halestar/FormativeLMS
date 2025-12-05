<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AuthSettings;
use App\Enums\Auth\LoginStages;
use App\Mail\ResetPasswordMail;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use App\Notifications\Auth\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;

class LoginForm extends Component
{
	// Form Fields
	public string $email = '';
	public bool $rememberMe = false;
	public string $password = '';

    public string $userAuthCode = '';
	
	// User Authentication
	public ?Person $user = null;
	public array $methodOptions = [];

	public LoginStages $stage = LoginStages::PromptEmail;
	
	//Verification Variables
	public string $authCode = '';
	public ?Carbon $authCodeExpires = null;
	public bool $canResetPassword = false;
	public ?Carbon $lockedUntil = null;
	
	public function mount()
	{
		//first, we check if there is a cookie set
		if(Cookie::has('remember-me'))
		{
			$this->email = Cookie::get('remember-me');
			$this->rememberMe = true;
		}
	}
	
	public function submitEmail()
	{
		$this->user = null;
        //validate the email
		$data = $this->validate([
			'email' => 'required|email|exists:people,email',
		]);
        //set the cookie or forget it, depending on the checkbox.
		if($this->rememberMe)
			Cookie::queue(cookie()->forever('remember-me', $data['email']));
		else
			Cookie::forget('remember-me');
        //get the user. We know they exist because of the email validation.
		$this->user = Person::where('email', $data['email'])
		                    ->first();
        //now, we check if the user has an auth connection.
        $connection = $this->user->authConnection;
		if(!$connection)
		{
			//in this case, we need to determine which authenticator (or more) apply to this user
			//based on the auth settings
            $authSettings = app(AuthSettings::class);
			$services = $authSettings->determineAuthentication($this->user);
            //if the service returned null, then this type of account is blocked.
			if(!$services)
			{
				//this is a bad error, so crap out.
				$this->stage = LoginStages::BlockedLogin;
				return;
			}
			//If we get a Collection back, the user must select their preferred method.
			if($services instanceof Collection && $services->count() > 1)
			{
				//in this case, we need to show the user the choosing form.
				$this->stage = LoginStages::PromptMethod;
				$this->methodOptions = [];
				foreach($services as $service)
					$this->methodOptions[$service->id] = ($service->getConnectionClass())::loginButton();
				return;
			}
            //if we get a single service back, we can just connect it.
			if($services instanceof Collection && $services->count() == 1)
				$services = $services->first();
            //if not, then $services is assumed to be a single service of type LmsIntegrationService.
            //so we establish the connection.
			$connection = $services->connect($this->user);
            //and save it to the user's auth connection
			$this->user->authConnection()
			           ->associate($connection);
			$this->user->save();
			$this->user->refresh();
		}
		//since we have a valid user, we know if we can reset the password.
		$this->canResetPassword = $connection->canResetPassword();
		//is the user locked?
		if($connection->isLocked())
        {
            //in this case, we show a locked user error
            $this->lockedUntil = $connection->lockedUntil();
            $this->stage = LoginStages::LockedUser;
        }
		elseif($connection->requiresPassword())
		{
            $this->stage = LoginStages::PromptPassword;
		}
		elseif($connection->requiresRedirection())
		{
			//in this case we redirect away from here
			$target = $connection->redirect();
			if($target)
				$this->redirect($target->getTargetUrl());
		}
	}


    public function submitMethod(IntegrationService $service)
    {
        $connection = $service->connect($this->user);
        if($connection)
        {
            $this->user->authConnection()
                ->associate($connection);
            $this->user->save();
        }
        $this->submitEmail();
    }
	
	public function submitPassword()
	{
		//we're here because the auth driver requires a password.
		$connection = $this->user->authConnection;
		//attempt the login using the acquired password
		if($connection->attemptLogin($this->password, $this->rememberMe, !$connection->mustChangePassword()))
		{
			//The password was authenticated. If the user does not need to change their password, they're in and can be redirected.
			if(!$connection->mustChangePassword())
			{
				$this->redirect($connection->completeLogin($this->user));
                return;
			}
			//else, we take them to the change password form.
			$this->stage = LoginStages::ResetPassword;
		}
		//the login failed, is the account now locked?
		if($connection->isLocked())
        {
            $this->lockedUntil = $connection->lockedUntil();
            $this->stage = LoginStages::LockedUser;
        }
		else
		{
			//the account is ok, just a wrong password, so we throw an error
			$this->addError('password', trans('auth.failed'));
			//and clear the password
			$this->password = '';
		}
	}
	
	
	public function forgotPassword()
	{
		//first, we generate a token
		$length = config('lms.auth_code_length');
		$this->authCode = '';
		for($i = 0; $i < $length; $i++)
			$this->authCode .= random_int(0, 9);
		$this->authCodeExpires = Carbon::now()
		                               ->addMinutes(config('lms.auth_code_timeout'));
		//go to the verification stage
		$this->stage = LoginStages::CodeVerification;
		//and send the code
        $this->user->notify(new ResetPasswordNotification($this->user, $this->authCode));
	}
	
	public function submitVerification()
	{
		if($this->authCodeExpires->isPast())
		{
			$this->timeoutTimer();
			return;
		}
		if($this->authCode != $this->userAuthCode)
		{
			$this->addError('userAuthCode', __('errors.auth.verification.incorrect'));
			return;
		}
		//in this case, the code is correct, so we reset the password.
		$this->stage = LoginStages::ResetPassword;
	}
	
	public function timeoutTimer()
	{
		$this->stage = LoginStages::CodeTimeout;
		$this->authCode = '';
		$this->authCodeExpires = null;
	}
	
	public function render()
	{
		return view('livewire.auth.login-form');
	}
	
	#[On('change-password-form.password-changed')]
	public function passwordReset()
	{
		//first, we are here because the user HAD to change their password?
		$connection = $this->user?->authConnection;
		if($connection)
		{
			if($connection->mustChangePassword())
			{
				//yes, so we will clear the change password directive and log the person in.
				$connection->setMustChangePassword(false);
				$this->redirect($connection->completeLogin($this->user));
				return;
			}
			$this->authCode = '';
			$this->authCodeExpires = null;
			$this->userAuthCode = '';
            $this->stage = LoginStages::PromptPassword;
		}
        else
        {
            $this->user = null;
            $this->email = '';
            $this->password = '';
            $this->authCode = '';
            $this->authCodeExpires = null;
            $this->userAuthCode = '';
            $this->stage = LoginStages::PromptEmail;
        }
	}
}
