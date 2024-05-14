<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (Application $app) {
            Route::prefix('api/v1')
                ->middleware('jwt.auth')
                ->name('api.v1.')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'jwt.auth' =>\App\Http\Middleware\JwtAuthMiddleware::class
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
