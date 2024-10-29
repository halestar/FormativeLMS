<?php

namespace App\Providers;

use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use App\Policies\PersonPolicy;
use App\Policies\RolePolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
        Gate::before(function(Person $person, $ability)
        {
            return $person->hasRole(SchoolRoles::$ADMIN)? true: null;
        });
        Gate::define('has-permission', function (Person $person, string $permission)
        {
            return $person->withoutGlobalScope('exclude_super_admin')->hasRole(SchoolRoles::$ADMIN) ||
                $person->hasPermissionTo($permission);
        });
        Gate::policy(SchoolRoles::class, RolePolicy::class);
        Gate::policy(Person::class, PersonPolicy::class);
        Paginator::useBootstrapFive();
    }
}
