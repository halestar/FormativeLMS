<?php

namespace App\Models\Integrations\Connections;

use App\Classes\Integrators\IntegrationsManager;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\IntegrationConnectionInterface;
use App\Models\Integrations\IntegrationConnection;
use App\Models\People\Person;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

abstract class AuthConnection extends IntegrationConnection implements IntegrationConnectionInterface
{
    /**
     * Auth Connections all have built-in defaults for the locked and locked_until fields. If you would like
     * to add more fields, simply override this variable.
     * @var array
     */
	protected static array $instanceDefaults = [];


	/**
	 * @return bool If set to true, then the login system will get a password from the user to pass
	 * to this authenticator
	 */
	abstract public static function requiresPassword(): bool;
	
	/**
	 * @return string A html-string of the button for the user to login with. This will only
	 * really be done when the user chooses their login method. It should not be a link, as it will be
	 * wrapped by one.
	 */
	abstract static public function loginButton(): string;
	
	/**
	 * @return bool If set to true, the Login system will redirect the user to another page that will
	 * handle the authentication.
	 */
	abstract public static function requiresRedirection(): bool;
	
	/**************************************************************
	 * PASSWORD FUNCTIONS: require if requiresRedirection() returns false
	 */


	/**
	 * @return array Will ALWAYS return the instance defaults defined by the subclass, plus the locked settings.
	 */
	final public static function getInstanceDefault(): array
	{
		// we need to ensure that the locking mechanism is always included in the instance defaults.
		return static::$instanceDefaults + ['locked' => false, 'locked_until' => null];
	}

	final public static function getSystemInstanceDefault(): array { return []; }

	/**
	 * @return bool Whether this authenticator can be assigned a password by an
	 * administrator
	 */
	abstract public function canChangePassword(): bool;
	
	/**
	 * @return bool Whether the user can reset their own password.
	 */
	abstract public function canResetPassword(): bool;
	
	/**
	 * This function will attempt to change a password for this authenticator.
	 * @param string $password THe new password
	 * @return bool Whether the operation was successful.
	 */
	abstract public function setPassword(string $password): bool;
	
	/**
	 * This function will attempt to authenticate the user with a provided
	 *   password. The user should never be logged in here.
	 * @param string $password The password from the login form
	 * @param bool $rememberMe Whether the user should be remembered
	 * @return bool Whether the user was successfully logged in.
	 */
	abstract function attemptLogin(string $password, bool $rememberMe): bool;
	
	/**
	 * @param string $password The password to test
	 * @return bool Whether the current password matches the one provided.
	 */
	abstract public function verifyPassword(string $password): bool;


	/**************************************************************
	 * USER LOCKING FUNCTIONS
	 */
	
	/**
	 * @return bool Whether this authenticator can set if user must change their password.
	 */
	abstract public function canSetMustChangePassword(): bool;
	
	/**
	 * Sets whether the user
	 * @param bool $mustChangePassword Optional, whether the password must be changed. Defaults to true
	 * @return void
	 */
	abstract public function setMustChangePassword(bool $mustChangePassword = true): void;
	
	/**
	 * @return bool Returns whether the user must change their password.
	 */
	abstract public function mustChangePassword(): bool;

	/**
	 * @param bool $locked Whether to lock this user. Defaults to true
	 * @param Carbon|null $timeout Until when to lock this user. Default is null, which is until manually unlocked.
	 * @return void
	 */
	public function lockUser(bool $locked = true, Carbon $until = null): void
	{
		//we need to be connected to a user
		$this->data->locked = $locked;
		if($until && $locked)
			$this->data->locked_until = $until->toDateTimeString();
		else
			$this->data->locked_until = null;
		$this->save();
	}

	/**
	 * @return Carbon|null Returns the Carbon date that this is locked until, or null if foreever.
	 */
	public function lockedUntil(): ?Carbon
	{
		if($this->isLocked())
			return $this->data->locked_until ? Carbon::parse($this->data->locked_until) : null;
		return null;
	}

	/**************************************************************
	 * FINAL FUNCTIONS
	 */

	/**
	 * @return bool Whether this user is locked.
	 */
	public function isLocked(): bool
	{
		if(!$this->data->locked)
			return false;
		if(!$this->data->locked_until)
			return true;
		//else, we need to determine if the time has past
		if(Carbon::now()
		         ->isAfter($this->data->locked_until))
		{
			$this->data->locked = false;
			$this->data->locked_until = null;
			$this->save();
			return false;
		}
		return true;
	}
	
	/**************************************************************
	 * REDIRECTION FUNCTIONS: require if requiresRedirection() returns true
	 */

    /**
     * This function will redirect the user to an external authentication service.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null The page to redirect to.
     */
	abstract public function redirect(): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null;

    /**
     * This function will be called as the callback from the external authentication service, once the user has been authenticated.
     * The important thing to do in this function is to get the authenticated user's Person model. Once that is done, call
     * the completeLogin() function to actually authenticate them into the system and return the page redirect that
     * the function returns to complete the login process.
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null The page to redirect to after the call is completed.
     */
    abstract public static function callback(): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\RedirectResponse|null;

    /**
     * This function will complete the login process by actually logging the user into the guard and completing any
     * steps before user login. It will return the URL where the user should be redirected to.
     * @param Person $user The user to log into the system
     * @return string The URL to redirect to.
     */
    final public function completeLogin(Person $user): string
    {
        Auth::guard()
            ->login($user, true);
        //regenerate the session
        request()
            ->session()
            ->regenerate();
        //we need to establish any connections that we are missing.
        $intManager = app(IntegrationsManager::class);
        //first, try all the Documents connections.
        foreach($intManager->unconnectedPersonalServices($user, IntegratorServiceTypes::DOCUMENTS) as $service)
            $service->connect($user);
        //we also need to establish any AI connections.
        foreach($intManager->unconnectedPersonalServices($user, IntegratorServiceTypes::AI) as $service)
            $service->connect($user);
        //in the case of faculty, we also do class management
        if($user->isTeacher())
        {
            foreach($intManager->unconnectedPersonalServices($user, IntegratorServiceTypes::CLASSES) as $service)
                $service->connect($user);
        }
		//in the case of parents, set up a student to view.
	    if($user->isParent())
	    {
			//do we have a saved student?
		    if(!$user->student_id)
		    {
				//pick the first student we find.
			    $user->student_id = $user->currentChildStudents()->first()?->id;
				$user->save();
		    }
	    }
        return redirect()->getIntendedUrl() ?? route('home');
    }
	
}