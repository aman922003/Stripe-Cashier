<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
            'stripe/webhook'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle exceptions if needed
    })
    ->withSchedule(function (Schedule $schedule) { 
        // Schedule commands as needed
        $schedule->command('send:emails')->everyMinute(); // Email sending command
    })
    ->create(); 