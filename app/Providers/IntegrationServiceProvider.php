<?php

namespace App\Providers;

use App\Classes\Integrators\IntegrationsManager;
use App\Classes\Integrators\SecureVault;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
	    $this->app->singleton(IntegrationsManager::class, fn(Application $app) => new IntegrationsManager());
		$this->app->singleton(SecureVault::class, fn(Application $app) => new SecureVault());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
	
	public function provides(): array
	{
		return [IntegrationsManager::class, SecureVault::class];
	}
	
}
