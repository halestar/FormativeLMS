<?php

namespace App\Classes\Integrators\Local;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Integrators\Local\Services\LocalAiService;
use App\Classes\Integrators\Local\Services\LocalAuthService;
use App\Classes\Integrators\Local\Services\LocalClassesService;
use App\Classes\Integrators\Local\Services\LocalDocumentsService;
use App\Classes\Integrators\Local\Services\LocalEmailService;
use App\Classes\Integrators\Local\Services\LocalSmsService;
use App\Classes\Integrators\Local\Services\LocalWorkFilesService;
use App\Enums\IntegratorServiceTypes;
use App\Http\Controllers\LocalIntegratorController;
use App\Models\Integrations\LmsIntegrator;
use Illuminate\Support\Facades\Route;

class LocalIntegrator extends LmsIntegrator
{
    public static function getPath(): string
    {
        return 'local';
    }

    public static function integratorName(): string
    {
        return __('integrators.local');
    }

    public static function integratorDescription(): string
    {
        return __('integrators.local.description');
    }

    public static function defaultData(): array
    {
        return [];
    }

    public static function getVersion(): string
    {
        return '0.3';
    }

    public static function canConnectToPeople(): bool
    {
        return true;
    }

    public static function canConnectToSystem(): bool
    {
        return false;
    }

    public static function canBeConfigured(): bool
    {
        return false;
    }

    public function registerServices(IntegrationsManager $manager, bool $overwrite = false): void
    {
        // register the auth service
        $manager->registerService($this, LocalAuthService::class, $overwrite);
        $manager->registerService($this, LocalDocumentsService::class, $overwrite);
        $manager->registerService($this, LocalWorkFilesService::class, $overwrite);
        $manager->registerService($this, LocalClassesService::class, $overwrite);
        $manager->registerService($this, LocalEmailService::class, $overwrite);
        $manager->registerService($this, LocalSmsService::class, $overwrite);
        $manager->registerService($this, LocalAiService::class, $overwrite);
    }

    public function isOutdated(): bool
    {
        return $this->version != LocalIntegrator::getVersion();
    }

    public function hasService(IntegratorServiceTypes $type): bool
    {
        return $type == IntegratorServiceTypes::AUTHENTICATION ||
            $type == IntegratorServiceTypes::DOCUMENTS ||
            $type == IntegratorServiceTypes::WORK ||
            $type == IntegratorServiceTypes::CLASSES ||
            $type == IntegratorServiceTypes::EMAIL ||
            $type == IntegratorServiceTypes::SMS ||
            $type == IntegratorServiceTypes::AI;
    }

    public function getImageUrl(): string
    {
        return asset('images/local_service.png');
    }

    public function publishRoutes(): void
    {
        // auth service
        Route::get('/auth', [LocalIntegratorController::class, 'auth'])
            ->name('auth.index');
        Route::patch('/auth', [LocalIntegratorController::class, 'auth_update'])
            ->name('auth.update');
        // documents service
        Route::get('/documents', [LocalIntegratorController::class, 'documents'])
            ->name('documents.index');
        Route::patch('/documents', [LocalIntegratorController::class, 'documents_update'])
            ->name('documents.update');
        // work files service
        Route::get('/work', [LocalIntegratorController::class, 'work'])
            ->name('work.index');
        Route::patch('/work', [LocalIntegratorController::class, 'work_update'])
            ->name('work.update');
        // classes Service
        Route::get('/classes', [LocalIntegratorController::class, 'classes'])
            ->name('classes.index');
        Route::patch('/classes', [LocalIntegratorController::class, 'classes_update'])
            ->name('classes.update');

        // classes preferences
        Route::get('/services/classes/preferences/{schoolClass}', [LocalIntegratorController::class, 'classPreferences'])
            ->name('services.classes.preferences')
            ->withoutMiddleware(['can:settings.integrators']);
        Route::post('/services/classes/preferences/{schoolClass}', [LocalIntegratorController::class, 'classPreferences_update'])
            ->name('services.classes.preferences.update')
            ->withoutMiddleware(['can:settings.integrators']);

        // ai system config
        Route::get('/services/ai/config', [LocalIntegratorController::class, 'aiPreferences'])
            ->name('services.ai.config');
        Route::post('/services/ai/config', [LocalIntegratorController::class, 'aiPreferences_update'])
            ->name('services.ai.config.update');

	    // ai personal register
	    Route::get('/services/ai/config/personal', [LocalIntegratorController::class, 'aiPersonalPreferences'])
		    ->name('services.ai.config.personal')
		    ->withoutMiddleware(['can:settings.integrators']);
	    Route::post('/services/ai/config/personal', [LocalIntegratorController::class, 'aiPersonalPreferences_update'])
		    ->name('services.ai.config.personal.update')
		    ->withoutMiddleware(['can:settings.integrators']);
    }
}
