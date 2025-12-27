<?php

namespace App\Http\Controllers;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LocalIntegratorController extends Controller
{
	public function auth()
	{
		//load the auth service
        $authService = LocalIntegrator::getService(IntegratorServiceTypes::AUTHENTICATION);
        //load or establish the system connection.
        $authSystemConnection = $authService->connectToSystem();
		$breadcrumb =
			[
				__('system.menu.integrators') => route('integrators.index'),
				$authService->integrator->name => '#',
				$authService->name => '#',
			];
		return view('integrators.local.auth', compact('authService', 'breadcrumb'));
	}
	
	public function auth_update(Request $request)
	{
		$authService = LocalIntegrator::getService(IntegratorServiceTypes::AUTHENTICATION);
		$data = $request->validate(
			[
				'maxAttempts' => 'required|integer|min:1|max:100',
				'decayMinutes' => 'required|integer|min:1|max:60',
				'lockoutTimeout' => 'required|integer|min:1|max:3600',
			]);
		$authService->data = $data;
		$authService->save();
		return redirect()
			->back()
			->with('success-status', __('integrators.local.auth.update.success'));
	}
	
	public function documents()
	{
		$documentsService = LocalIntegrator::getService(IntegratorServiceTypes::DOCUMENTS);
		$disks = config('filesystems.disks');
		$breadcrumb =
			[
				__('system.menu.integrators') => route('integrators.index'),
				$documentsService->integrator->name => '#',
				$documentsService->name => '#',
			];
		return view('integrators.local.documents', compact('documentsService', 'disks', 'breadcrumb'));
	}
	
	public function documents_update(Request $request)
	{
		$documentsService = LocalIntegrator::getService(IntegratorServiceTypes::DOCUMENTS);
		$data = $request->validate(
			[
				'documents_disk' => ['required', Rule::in(array_keys(config('filesystems.disks')))],
			]);
		$documentsService->data = $data;
		$documentsService->save();
		return redirect()
			->back()
			->with('success-status', __('integrators.local.documents.update.success'));
	}
	
	public function work()
	{
		$workService = LocalIntegrator::getService(IntegratorServiceTypes::WORK);
		$disks = config('filesystems.disks');
		$breadcrumb =
			[
				__('system.menu.integrators') => route('integrators.index'),
				$workService->integrator->name => '#',
				$workService->name => '#',
			];
		return view('integrators.local.work', compact('workService', 'disks', 'breadcrumb'));
	}
	
	public function work_update(Request $request)
	{
        $workService = LocalIntegrator::getService(IntegratorServiceTypes::WORK);
		$data = $request->validate(
			[
				'work_disk' => ['required', Rule::in(array_keys(config('filesystems.disks')))],
			]);
		$workService->data = $data;
		$workService->save();
		return redirect()
			->back()
			->with('success-status', __('integrators.local.work.update.success'));
	}

	public function classes()
	{
        $classesService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
        $breadcrumb =
            [
                __('system.menu.integrators') => route('integrators.index'),
                $classesService->integrator->name => '#',
                $classesService->name => '#',
            ];
        return view('integrators.local.classes.settings', compact('classesService', 'breadcrumb'));
	}

    public function classes_update(Request $request)
    {
	    $classesService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
		$validation = [];
		foreach((array)$classesService->data->available as $widgetClass => $widgetName)
			$validation[$widgetClass] = ['required', Rule::in(['required', 'optional', 'block'])];
		$data = $request->validate($validation);
		$settings = $classesService->data;
		$settings->required = [];
		$settings->optional = [];
	    foreach((array)$classesService->data->available as $widgetClass => $widgetName)
	    {
			if($data[$widgetClass] == 'required')
				$settings->required[] = $widgetClass;
			else if($data[$widgetClass] == 'optional')
				$settings->optional[] = $widgetClass;
	    }
		$classesService->data = $settings;
		$classesService->save();
		//the last thing we do is invalidate the layout for all the classes using this service
	    DB::table('class_sessions')
		    ->join('integration_connections', 'integration_connections.id', '=', 'class_sessions.class_management_id')
		    ->where('integration_connections.service_id', $classesService->id)
		    ->update(['class_sessions.layout' => null]);
		return redirect()
			->back()
			->with('success-status', __('integrators.local.classes.update.success'));
    }
}
