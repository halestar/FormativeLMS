<?php

namespace App\Classes\Integrators\Local;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Integrators\Local\Connections\LocalEmailConnection;
use App\Classes\Integrators\Local\Services\LocalAuthService;
use App\Classes\Integrators\Local\Services\LocalClassesService;
use App\Classes\Integrators\Local\Services\LocalDocumentsService;
use App\Classes\Integrators\Local\Services\LocalEmailService;
use App\Classes\Integrators\Local\Services\LocalSmsService;
use App\Classes\Integrators\Local\Services\LocalWorkFilesService;
use App\Enums\IntegratorServiceTypes;
use App\Http\Controllers\LocalIntegratorController;
use App\Models\Integrations\LmsIntegrator;
use App\Models\People\Person;
use Illuminate\Support\Facades\Route;

class LocalIntegrator extends LmsIntegrator
{
	static function integratorName(): string
	{
		return __('integrators.local');
	}
	
	static function integratorDescription(): string
	{
		return __('integrators.local.description');
	}
	
	public static function defaultData(): array
	{
		return [];
	}
	
	public static function canConnectToPeople(): bool
	{
		return true;
	}
	
	public static function canConnectToSystem(): bool
	{
		return false;
	}
	
	public static function getPath(): string
	{
		return "local";
	}
	
	public static function canBeConfigured(): bool
	{
		return false;
	}
	
	public function registerServices(IntegrationsManager $manager, bool $overwrite = false): void
	{
		//register the auth service
		$manager->registerService($this, LocalAuthService::class, $overwrite);
		$manager->registerService($this, LocalDocumentsService::class, $overwrite);
		$manager->registerService($this, LocalWorkFilesService::class, $overwrite);
		$manager->registerService($this, LocalClassesService::class, $overwrite);
        $manager->registerService($this, LocalEmailService::class, $overwrite);
        $manager->registerService($this, LocalSmsService::class, $overwrite);
	}
	
	public function isOutdated(): bool
	{
		return ($this->integrator->version != LocalIntegrator::getVersion());
	}
	
	public static function getVersion(): string
	{
		return "0.2";
	}
	
	public function hasService(IntegratorServiceTypes $type): bool
	{
		return ($type == IntegratorServiceTypes::AUTHENTICATION ||
			$type == IntegratorServiceTypes::DOCUMENTS ||
			$type == IntegratorServiceTypes::WORK ||
			$type == IntegratorServiceTypes::CLASSES ||
            $type == IntegratorServiceTypes::EMAIL ||
            $type == IntegratorServiceTypes::SMS);
	}
	
	public function publishRoutes(): void
	{
		//auth service
		Route::get('/auth', [LocalIntegratorController::class, 'auth'])
		     ->name('auth.index');
		Route::patch('/auth', [LocalIntegratorController::class, 'auth_update'])
		     ->name('auth.update');
		//documents service
		Route::get('/documents', [LocalIntegratorController::class, 'documents'])
		     ->name('documents.index');
		Route::patch('/documents', [LocalIntegratorController::class, 'documents_update'])
		     ->name('documents.update');
		//work files service
		Route::get('/work', [LocalIntegratorController::class, 'work'])
		     ->name('work.index');
		Route::patch('/work', [LocalIntegratorController::class, 'work_update'])
		     ->name('work.update');
		//classes Service
		Route::get('/classes', [LocalIntegratorController::class, 'classes'])
		     ->name('classes.index');
        Route::patch('/classes', [LocalIntegratorController::class, 'classes_update'])
            ->name('classes.update');
	}
	
	public function configurationUrl(): string
	{
		return '';
	}
	
	public function getImageUrl(): string
	{
		return asset('images/local_service.png');
	}
	
	public function isIntegrated(Person $person): bool { return false; }
	
	public function integrationUrl(Person $person): string { return ''; }
	
	public function removeIntegration(Person $person): void {}
	
	protected function canIntegrate(Person $person): bool { return false; }
	
}