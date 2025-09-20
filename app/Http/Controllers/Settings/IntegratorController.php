<?php

namespace App\Http\Controllers\Settings;

use App\Classes\Integrators\IntegrationsManager;
use App\Enums\IntegratorServiceTypes;
use App\Http\Controllers\Controller;
use App\Models\Integrations\IntegrationService;
use App\Models\Integrations\Integrator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class IntegratorController extends Controller
{
	
	public static function middleware()
	{
		return
			[
				new Middleware('auth', except: ['auth_callback']),
				new Middleware('can:settings.integrators', except: ['auth_callback']),
			];
	}
	
	public function index()
	{
		$breadcrumb =
			[
				__('system.menu.integrators') => '#',
			];
		return view('integrators.index', compact('breadcrumb'));
	}
	
	public function register(Integrator $integrator, IntegrationsManager $manager)
	{
		$manager->registerIntegrator($integrator->className);
		return redirect()
			->route('integrators.index')
			->with('success-status', __('integrators.register.update.success'));
	}
	
	public function clear(Integrator $integrator, IntegrationsManager $manager)
	{
		$manager->registerIntegrator($integrator->className, true);
		return redirect()
			->route('integrators.index')
			->with('success-status', __('integrators.register.clear.success'));
	}
	
	public function servicePermissions(IntegrationService $service)
	{
		$breadcrumb =
			[
				__('system.menu.integrators') => route('integrators.index'),
				$service->integrator->name => $service->integrator->configurable ? $service->integrator->configurationUrl() : '#',
				$service->name => '#',
			];
		return view('integrators.permissions', compact('breadcrumb', 'service'));
	}
	
	public function auth_callback(Request $request, Integrator $integrator)
	{
		//determine the auth service
		$auth = $integrator->services()
		                   ->ofType(IntegratorServiceTypes::AUTHENTICATION)
		                   ->first();
		return ($auth->getConnectionClass())::callback();
	}
}
