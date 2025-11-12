<?php

return [
	App\Providers\AppServiceProvider::class,
	App\Providers\IntegrationServiceProvider::class,
	App\Providers\SynthServiceProvider::class,
	App\Providers\SystemSettingsProvider::class,
	halestar\DiCmsBlogger\Providers\DiCmsBloggerServiceProvider::class,
	halestar\FablmsGoogleIntegrator\GoogleIntegratorServiceProvider::class,
	halestar\LaravelDropInCms\Providers\CmsServiceProvider::class,
];
