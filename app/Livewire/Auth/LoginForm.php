<?php

namespace App\Livewire\Auth;

use App\Classes\Settings\AuthSettings;
use App\Mail\ResetPasswordMail;
use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
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

	private function lockUser()
	{
		$this->lockedUser = true;
		$this->gotoStage('promptEmail');
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
		$this->user = Person::where('email', $data['email'])->first();
		$authenticator = $this->user->auth_driver;
		if(!$authenticator)
		{
			//in this case, we need to determine which authenticator (or more) apply to this user
			//based on the auth settings
			$authenticator = $this->authSettings->determineAuthentication($this->user);
			if(!$authenticator)
			{
				//this is a bad error, so crap out.
				$this->accountError = true;
				return;
			}
			//do we have a single authenticator? or does the user have to choose?
			if(is_array($authenticator))
			{
				//in this case, we need to show the user the choosing form.
				$this->gotoStage('promptMethod');
				$this->methodOptions = [];
				foreach($authenticator as $driver)
					$this->methodOptions[$driver] = config('auth.drivers.' . $driver . '.class');
				return;
			}
			else
			{
				//in this case, we need to save the choice for next time.
				$this->user->auth_driver = $authenticator;
				$this->user->save();
				$authenticator = $this->user->auth_driver;
			}
		}
		//is the user locked?
		if($authenticator->isLocked()) //in this case, we show a locked user error
			$this->lockUser();
		elseif($authenticator->requiresPassword())
		{
			//In this case we have a valid user that needs to be prompted for a password.
			$this->gotoStage('promptPassword');
			$this->lockedUser = false;
		}
		elseif($authenticator->requiresRedirection())
		{
			//in this case we redirect away from here
			$target = $authenticator->redirect();
			if($target)
				$this->redirect($target->getTargetUrl());
		}
	}

	public function submitMethod(string $method)
	{
		$this->user->auth_driver = $method;
		$this->user->save();
		$this->submitEmail();
	}

	public function submitPassword()
	{
		//we're here because the auth driver requires a password.
		$authDriver = $this->user->auth_driver;
		//attempt the login using the acquired password
		if($authDriver->attemptLogin($this->password, $this->rememberMe))
		{
			//Success! the user is now logged in, so we redirect to wherever we need to.
			$this->redirectIntended(route('home'));
			return;
		}
		//the login failed, is the account now locked?
		if($this->user->auth_driver->isLocked())
			$this->lockUser();
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
		for ($i = 0; $i < $length; $i++)
			$this->authCode .= random_int(0, 9);
		$this->authCodeExpires = Carbon::now()->addMinutes(config('lms.auth_code_timeout'));
		//go to the verification stage
		$this->gotoStage('codeVerification');
		//and send the code
		Mail::to($this->user->system_email)
			->send(new ResetPasswordMail($this->user, $this->authCode));
	}

	public function timeoutTimer()
	{
		$this->gotoStage('codeTimeout');
		$this->authCode = '';
		$this->authCodeExpires = null;
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

    public function render()
    {
        return view('livewire.auth.login-form');
    }

	#[On('change-password-form.password-changed')]
	public function passwordReset()
	{
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
