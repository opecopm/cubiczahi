<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\UserType;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request): ?string {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->routeIs('customer.*')) {
                return route('customer.login');
            }

            return route('admin.login');
        });

        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'user_type' => UserType::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
