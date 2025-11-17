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
    ->withMiddleware(function (Middleware $middleware) {
        // Alias de middlewares de ruta
        $middleware->alias([
            'auth'         => \Illuminate\Auth\Middleware\Authenticate::class,
            'admin'        => \App\Http\Middleware\AdminMiddleware::class,
            'admin.master' => \App\Http\Middleware\AdminMasterMiddleware::class,
        ]);
    })
    ->withExceptions(function ($exceptions) {
        //
    })->create();