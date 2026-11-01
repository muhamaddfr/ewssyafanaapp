<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Set zona waktu & locale Indonesia (WIB - Asia/Jakarta UTC+7)
        date_default_timezone_set(config('app.timezone', 'Asia/Jakarta'));
        Carbon::setLocale(config('app.locale', 'id'));

        // Default pagination view ultra-clean
        Paginator::defaultView('vendor.pagination.custom');
    }
}
