<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
            'webhooks/hub-qris/notification',
        ]);

        // Cookie Meta Pixel dibuat oleh JavaScript, jadi jangan didekripsi Laravel.
        $middleware->encryptCookies(except: [
            '_fbp',
            '_fbc',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
