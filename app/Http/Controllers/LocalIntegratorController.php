<?php

namespace App\Http\Controllers;

use App\Enums\IntegratorServiceTypes;
use App\Models\Integrations\IntegrationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocalIntegratorController extends Controller
{
	public function auth()
	{
		//load the auth service
		$authService = IntegrationService::select('integration_services.*')
		                                 ->join('integrators', 'integrators.id', '=',
			                                 'integration_services.integrator_id')
		                                 ->where('integration_services.service_type',
			                                 IntegratorServiceTypes::AUTHENTICATION)
		                                 ->where('integrators.path', 'local')
		                                 ->first();
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
		$authService = IntegrationService::select('integration_services.*')
		                                 ->join('integrators', 'integrators.id', '=',
			                                 'integration_services.integrator_id')
		                                 ->where('integration_services.service_type',
			                                 IntegratorServiceTypes::AUTHENTICATION)
		                                 ->where('integrators.path', 'local')
		                                 ->first();
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
		$documentsService = IntegrationService::select('integration_services.*')
		                                      ->join('integrators', 'integrators.id', '=',
			                                      'integration_services.integrator_id')
		                                      ->where('integration_services.service_type',
			                                      IntegratorServiceTypes::DOCUMENTS)
		                                      ->where('integrators.path', 'local')
		                                      ->first();
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
		$documentsService = IntegrationService::select('integration_services.*')
		                                      ->join('integrators', 'integrators.id', '=',
			                                      'integration_services.integrator_id')
		                                      ->where('integration_services.service_type',
			                                      IntegratorServiceTypes::DOCUMENTS)
		                                      ->where('integrators.path', 'local')
		                                      ->first();
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
		$workService = IntegrationService::select('integration_services.*')
		                                 ->join('integrators', 'integrators.id', '=',
			                                 'integration_services.integrator_id')
		                                 ->where('integration_services.service_type', IntegratorServiceTypes::WORK)
		                                 ->where('integrators.path', 'local')
		                                 ->first();
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
		$workService = IntegrationService::select('integration_services.*')
		                                 ->join('integrators', 'integrators.id', '=',
			                                 'integration_services.integrator_id')
		                                 ->where('integration_services.service_type', IntegratorServiceTypes::WORK)
		                                 ->where('integrators.path', 'local')
		                                 ->first();
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
}
