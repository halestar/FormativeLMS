<?php

use App\Classes\Integrators\IntegrationsManager;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasskeyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\People\PersonalPreferencesController;
use App\Http\Controllers\Settings\IntegratorController;
use App\Http\Controllers\Settings\SchoolSettingsController;
use App\Http\Controllers\Substitutes\SubstituteAccessController;
use App\Http\Middleware\MFAuthenticate;
use App\Livewire\Ai\EditModelPrompt;
use App\Models\Integrations\Integrator;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/********************************************************************
 * AUTHENTICATION ROUTES
 */
Route::livewire('/login', 'auth.login-form')->name('login');
Route::controller(LoginController::class)
     ->group(function ()
     {
	     Route::post('/logout', 'logout')
	          ->name('logout');
	     Route::get('/impersonate/{person}', 'impersonate')
	          ->middleware(['auth', 'can:people.impersonate'])
	          ->name('impersonate');
	     Route::get('/unimpersonate', 'unimpersonate')
	          ->middleware(['auth'])
	          ->name('unimpersonate');
	     Route::get('/login/link', 'linkLogin')
	          ->name('login.link');

	     // view child student
	     Route::get('/select/child/{student}', 'viewChild')
	          ->name('view.child')
	          ->middleware([
		          'auth', 'role:' . SchoolRoles::$PARENT . '|' . SchoolRoles::$OLD_PARENT, MFAuthenticate::class,
	          ]);
     });

/********************************************************************
 * INTEGRATION ROUTES
 */
if (Schema::hasTable('integrators'))
{
	$manager = app()->make(IntegrationsManager::class);
	Route::prefix(Integrator::INTEGRATOR_URL_PREFIX)
	     ->name(Integrator::INTEGRATOR_ACTION_PREFIX)
	     ->middleware(['can:settings.integrators', 'auth'])
	     ->group(function () use ($manager)
	     {
		     // configuration route
		     Route::get('/', [IntegratorController::class, 'index'])
		          ->name('index');
		     // Integrator Service Permissions
		     Route::get('/services/{service}/permissions', [IntegratorController::class, 'servicePermissions'])
		          ->name('services.permissions');

		     foreach ($manager->availableIntegrators() as $integrator)
		     {

			     Route::prefix($integrator->path)
			          ->name(str_replace('/', '.', $integrator->path) . '.')
			          ->group(function () use ($integrator)
			          {
				          $integrator->publishRoutes();
			          });
		     }
		     // auth callback routes
		     Route::get('/{integrator:path}/auth', [IntegratorController::class, 'auth_callback'])
		          ->name('auth.callback')
		          ->withoutMiddleware(['auth', 'can:settings.integrators']);

		     // Update integrator registration route
		     Route::get('/{integrator:path}/register', [IntegratorController::class, 'register'])
		          ->name('register');
		     // Clear integrator registration route
		     Route::get('/{integrator:path}/clear', [IntegratorController::class, 'clear'])
		          ->name('clear');
	     });
}

/********************************************************************
 * AI ROUTES
 */
Route::livewire('/ai/prompt/{aiPrompt}', EditModelPrompt::class)
     ->middleware(['auth', MFAuthenticate::class])
     ->name('ai.prompt.editor');

/********************************************************************
 * HOME
 */
Route::get('/home', [HomeController::class, 'index'])
     ->middleware(['auth', MFAuthenticate::class])
     ->name('home');

// settings
Route::post('/settings', [SchoolSettingsController::class, 'setSessionSetting']);
Route::get('/settings', [SchoolSettingsController::class, 'getSessionSetting']);
Route::post('/preference', [PersonalPreferencesController::class, 'setPersonalPreference']);

// Substitutes routes
Route::get('/s/v', [SubstituteAccessController::class, 'verify'])->name('subs.verify');
Route::post('/s/v', [SubstituteAccessController::class, 'verifySub'])->name('subs.verify.update');
Route::post('/s/r', [SubstituteAccessController::class, 'accept'])->name('subs.request.accept');
Route::get('/s/r', [SubstituteAccessController::class, 'request'])->name('subs.request');

// language changes
Route::post('/langsw', function (Request $request)
{
	session(['language' => $request->input('lang')]);

	return redirect()->back();
})->name('language.switch');

//passkey routes
Route::post('passkeys/authenticate', PasskeyController::class)
     ->name('passkeys.login');
Route::get('passkeys/authentication-options', \Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController::class)
     ->name('passkeys.authentication_options');

//MFA routes
Route::livewire('/mfa', 'pages::auth.mfa')
     ->name('mfa');

// Livewire update route to allow for other, catch all routes.
Livewire::setUpdateRoute(function ($handle, $path)
{
	return Route::post($path, $handle)->name('livewire.update');
});
