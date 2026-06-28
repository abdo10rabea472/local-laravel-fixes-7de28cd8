<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
            'ajax.response' => \App\Http\Middleware\AjaxResponse::class,
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'set.currency' => \App\Http\Middleware\SetCurrency::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\AjaxResponse::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\SetCurrency::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/shipping/*/webhook',
            'payments/verify/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
