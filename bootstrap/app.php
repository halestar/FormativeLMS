<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function()
        {
            Route::middleware(['web', 'auth'])
                ->prefix('people')
                ->name('people.')
                ->group(base_path('routes/people.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('locations')
                ->name('locations.')
                ->group(base_path('routes/locations.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('academics')
                ->name('subjects.')
                ->group(base_path('routes/subjects.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('settings')
                ->name('settings.')
                ->group(base_path('routes/settings.php'));

            \halestar\LaravelDropInCms\DiCMS::publicRoutes();
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
