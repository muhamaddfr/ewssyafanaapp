<?php

use App\Http\Middleware\EnsureKeuangan;
use App\Http\Middleware\EnsurePemilik;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Setiap hari jam 22:40 — mode produksi (tanpa --cutoff)
        $schedule->command('ews:forecast')->dailyAt('06:55');
    })
    ->withMiddleware(function (Middleware $middleware) {
        // Pendaftaran alias middleware untuk kontrol akses ber-peran
        $middleware->alias([
            'pemilik'  => EnsurePemilik::class,
            'keuangan' => EnsureKeuangan::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
