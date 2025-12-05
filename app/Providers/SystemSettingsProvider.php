<?php

namespace App\Providers;

use App\Classes\SessionSettings;
use App\Classes\Settings\AuthSettings;
use App\Classes\Settings\CommunicationSettings;
use App\Classes\Settings\IdSettings;
use App\Classes\Settings\SchoolSettings;
use App\Classes\Settings\StorageSettings;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class SystemSettingsProvider extends ServiceProvider implements DeferrableProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->singleton(AuthSettings::class, fn(Application $app) => AuthSettings::instance());
		$this->app->singleton(SchoolSettings::class, fn(Application $app) => SchoolSettings::instance());
		$this->app->singleton(IdSettings::class, fn(Application $app) => IdSettings::instance());
		$this->app->singleton(StorageSettings::class, fn(Application $app) => StorageSettings::instance());
        $this->app->singleton(CommunicationSettings::class, fn(Application $app) => CommunicationSettings::instance());
		$this->app->bind(SessionSettings::class, fn(Application $app) => SessionSettings::instance());
	}
	
	/**
	 * Bootstrap services.
	 */
	public function boot(): void
	{
		Password::defaults(function()
		{
			$settings = app(AuthSettings::class);
			$req = Password::min($settings->min_password_length)
			               ->letters();
			if($settings->numbers)
				$req = $req->numbers();
			if($settings->upper)
				$req = $req->mixedCase();
			if($settings->symbols)
				$req = $req->symbols();
			return $req;
		});
	}
	
	public function provides(): array
	{
		return [AuthSettings::class, SchoolSettings::class, IdSettings::class, StorageSettings::class, CommunicationSettings::class];
	}
}
