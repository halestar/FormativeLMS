<?php

namespace App\Providers;

use App\Classes\Synths\AuthenticationDesignationSynth;
use App\Classes\Synths\ClassSessionLayoutManagerSynth;
use App\Classes\Synths\ClassTabsSynth;
use App\Classes\Synths\ClassTabSynth;
use App\Classes\Synths\ClassWidgetSynth;
use App\Classes\Synths\IdCardElementSynth;
use App\Classes\Synths\IdCardSynth;
use App\Classes\Synths\NameConstructorSynth;
use App\Classes\Synths\NameTokenSynth;
use App\Classes\Synths\RoleFieldSynth;
use App\Classes\Synths\RubricSynth;
use App\Classes\Synths\TopAnnouncementWidgetSynth;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use App\Policies\PersonPolicy;
use App\Policies\RolePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
        Livewire::propertySynthesizer(
            [
                RoleFieldSynth::class,
                NameConstructorSynth::class,
                NameTokenSynth::class,
                TopAnnouncementWidgetSynth::class,
                ClassSessionLayoutManagerSynth::class,
                ClassTabsSynth::class,
                ClassTabSynth::class,
                ClassWidgetSynth::class,
                RubricSynth::class,
                IdCardSynth::class,
                IdCardElementSynth::class,
	            AuthenticationDesignationSynth::class,
            ]);
    }
}
