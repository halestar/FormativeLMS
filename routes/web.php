<?php

use App\Classes\Integrators\IntegrationsManager;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Settings\IntegratorController;
use App\Http\Controllers\Settings\SystemTablesController;
use App\Models\Integrations\Integrator;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/********************************************************************
 * AUTHENTICATION ROUTES
 */
Route::get('/login', [LoginController::class, 'showLoginForm'])
     ->name('login');
Route::post('/logout', [LoginController::class, 'logout'])
     ->name('logout');
Route::get('/impersonate/{person}', [LoginController::class, 'impersonate'])
     ->name('impersonate');
Route::get('/unimpersonate', [LoginController::class, 'unimpersonate'])
     ->name('unimpersonate');

//view child student
Route::get('/select/child/{student}', [LoginController::class, 'viewChild'])
	->name('view.child')
	->middleware('role:' . SchoolRoles::$PARENT . "|" . SchoolRoles::$OLD_PARENT);

/********************************************************************
 * INTEGRATION ROUTES
 */
if(Schema::hasTable('integrators'))
{
	$manager = app()->make(IntegrationsManager::class);
	Route::prefix(Integrator::INTEGRATOR_URL_PREFIX)
	     ->name(Integrator::INTEGRATOR_ACTION_PREFIX)
	     ->middleware('can:settings.integrators')
	     ->group(function() use ($manager)
	     {
		     //configuration route
		     Route::get('/', [IntegratorController::class, 'index'])
		          ->name('index');
		     //Integrator Service Permissions
		     Route::get('/services/{service}/permissions', [IntegratorController::class, 'servicePermissions'])
		          ->name('services.permissions');
		     
		     foreach($manager->availableIntegrators() as $integrator)
		     {
			     
			     Route::prefix($integrator->path)
			          ->name(str_replace('/', '.', $integrator->path) . ".")
			          ->group(function() use ($integrator)
			          {
				          $integrator->publishRoutes();
			          });
		     }
		     //auth callback routes
		     Route::get('/{integrator:path}/auth', [IntegratorController::class, 'auth_callback'])
		          ->name('auth.callback')
		          ->withoutMiddleware(['auth', 'can:settings.integrators']);
		     
		     //Update integrator registration route
		     Route::get('/{integrator:path}/register', [IntegratorController::class, 'register'])
		          ->name('register');
		     //Clear integrator registration route
		     Route::get('/{integrator:path}/clear', [IntegratorController::class, 'clear'])
		          ->name('clear');
	     });
}

/********************************************************************
 * AI ROUTES
 */
Route::get('/ai/prompt/{aiPrompt}', \App\Livewire\Ai\EditModelPrompt::class)
     ->name('ai.prompt.editor');

/********************************************************************
 * HOME
 */
Route::get('/home', [HomeController::class, 'index'])
     ->name('home');


//settings
Route::post('/settings', [\App\Http\Controllers\Settings\SchoolSettingsController::class, 'setSessionSetting']);
Route::get('/settings', [\App\Http\Controllers\Settings\SchoolSettingsController::class, 'getSessionSetting']);

//language changes
Route::post('/langsw', function(Request $request)
{
	session(['language' => $request->input('lang')]);
	return redirect()->back();
})->name('language.switch');







