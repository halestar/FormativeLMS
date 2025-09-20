<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AuthSettings;
use App\Mail\ResetPasswordMail;
use App\Models\Integrations\IntegrationService;
use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
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
	
	// User Authentication
	public ?Person $user = null;
	public array $methodOptions = [];
	/* @var AuthSettings $authSettings */
	public SystemSetting $authSettings;
	
	//Stage Tracking
	public bool $promptEmail = true;
	public bool $promptMethod = false;
	public bool $promptPassword = false;
	public bool $codeVerification = false;
	public bool $codeTimeout = false;
	public bool $resetPassword = false;
	
	// Account Flags
	public bool $lockedUser = false;
	public bool $accountError = false;
	
	//Verification Variables
	public string $authCode = '';
	public string $userAuthCode = '';
	public ?Carbon $authCodeExpires = null;
	public bool $canResetPassword = false;
	public ?Carbon $lockedUntil = null;
	
	public function mount(AuthSettings $authSettings)
	{
		$this->authSettings = $authSettings;
		//first, we check if there is a cookie set
		if(Cookie::has('remember-me'))
		{
			$this->email = Cookie::get('remember-me');
			$this->rememberMe = true;
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
	
	public function submitEmail()
	{
		$this->user = null;
		$data = $this->validate([
			'email' => 'required|email|exists:people,email',
		]);
		if($this->rememberMe)
			Cookie::queue(cookie()->forever('remember-me', $data['email']));
		else
			Cookie::forget('remember-me');
		$this->user = Person::where('email', $data['email'])
		                    ->first();
		if(!$this->user->authConnection)
		{
			//in this case, we need to determine which authenticator (or more) apply to this user
			//based on the auth settings
			$services = $this->authSettings->determineAuthentication($this->user);
			if(!$services)
			{
				//this is a bad error, so crap out.
				$this->accountError = true;
				return;
			}
			//do we have a single authenticator? or does the user have to choose?
			if($services instanceof Collection && $services->count() > 1)
			{
				//in this case, we need to show the user the choosing form.
				$this->gotoStage('promptMethod');
				$this->methodOptions = [];
				foreach($services as $service)
					$this->methodOptions[$service->id] = ($service->getConnectionClass())::loginButton();
				return;
			}
			if($services instanceof Collection && $services->count() == 1)
				$services = $services->first();
			$connection = $services->connect($this->user);
			Log::debug(print_r($connection, true));
			$this->user->authConnection()
			           ->associate($connection);
			$this->user->save();
			$this->user->refresh();
		}
		$connection = $this->user->authConnection;
		//since we have a valid user, we know if we can reset the password.
		$this->canResetPassword = $connection->canResetPassword();
		//is the user locked?
		if($connection->isLocked()) //in this case, we show a locked user error
			$this->lockUser($connection->lockedUntil());
		elseif($connection->requiresPassword())
		{
			//In this case we have a valid user that needs to be prompted for a password.
			$this->gotoStage('promptPassword');
			$this->lockedUser = false;
		}
		elseif($connection->requiresRedirection())
		{
			//in this case we redirect away from here
			$target = $connection->redirect();
			if($target)
				$this->redirect($target->getTargetUrl());
		}
	}
	
	public function gotoStage($stage)
	{
		$this->promptEmail = false;
		$this->promptMethod = false;
		$this->promptPassword = false;
		$this->codeVerification = false;
		$this->codeTimeout = false;
		$this->resetPassword = false;
		$this->$stage = true;
	}
	
	private function lockUser(Carbon $until = null)
	{
		$this->lockedUser = true;
		$this->lockedUntil = $until;
		$this->gotoStage('promptEmail');
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
				$this->redirectIntended(route('home'));
				return;
			}
			//else, we take them to the change password form.
			$this->gotoStage('resetPassword');
		}
		//the login failed, is the account now locked?
		if($connection->isLocked())
			$this->lockUser($connection->lockedUntil());
		else
		{
			//the account is ok, just a wrong password, so we throw an error
			$this->addError('password', trans('auth.failed'));
			//and clear the password
			$this->password = '';
		}
	}
	
	public function returnToEmail()
	{
		$this->user = null;
		$this->gotoStage('promptEmail');
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
		$this->gotoStage('codeVerification');
		//and send the code
		Mail::to($this->user->system_email)
		    ->send(new ResetPasswordMail($this->user, $this->authCode));
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
		$this->gotoStage('resetPassword');
	}
	
	public function timeoutTimer()
	{
		$this->gotoStage('codeTimeout');
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
		$connection = $this->user->authConnection;
		if($connection)
		{
			if($connection->mustChangePassword())
			{
				//yes, so we will clear the change passweord directive and log the person in.
				$connection->setMustChangePassword(false);
				Auth::guard()
				    ->login($this->user, $this->rememberMe);
				//regenerate the session
				request()
					->session()
					->regenerate();
				$this->redirectIntended(route('home'));
				return;
			}
			$this->authCode = '';
			$this->authCodeExpires = null;
			$this->userAuthCode = '';
			if($this->user)
			{
				$this->gotoStage('promptPassword');
			}
			else
			{
				$this->gotoStage('promptEmail');
			}
		}
	}
}
