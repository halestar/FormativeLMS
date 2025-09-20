<?php

namespace App\Providers;

use App\Classes\SessionSettings;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use App\Policies\PersonPolicy;
use App\Policies\RolePolicy;
use App\View\Composers\IntegratorConfigurationComposer;
use DOMDocument;
use Illuminate\Foundation\Application;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
	    $this->app->bind(SessionSettings::class, fn(Application $app) => SessionSettings::instance());
	    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
		if(app()->environment('production') || env('FORCE_SSL', false))
			URL::forceScheme('https');
        Gate::before(function(Person $person, $ability)
        {
            return $person->hasRole(SchoolRoles::$ADMIN)? true: null;
        });
        Gate::define('has-permission', function (Person $person, string $permission)
        {
            return $person->hasRole(SchoolRoles::$ADMIN) ||
                $person->hasPermissionTo($permission);
        });
        Gate::policy(SchoolRoles::class, RolePolicy::class);
        Gate::policy(Person::class, PersonPolicy::class);
        Paginator::useBootstrapFive();
		//Composer Views
	    View::composer('layouts.integrations', IntegratorConfigurationComposer::class);
		Blade::directive('svg', function ($arguments)
		{
			$args = array_pad(explode(',', trim($arguments, "() ")), 2, '');
			$path = trim($args[0], "' ");
			$class = trim($args[1], "' ");
			// Create the dom document as per the other answers
			$svg = new DOMDocument();
			$svg->load(public_path($path));
			if($class != '')
				$svg->documentElement->setAttribute("class", $class);
			$output = $svg->saveXML($svg->documentElement);
			
			return $output;
		});
    }
}
