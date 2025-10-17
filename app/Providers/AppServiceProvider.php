<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use App\Models\Order;
use App\Observers\OrderObserver;

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
        // Register the Order observer
        Order::observe(OrderObserver::class);
        // Run invoice processing once per day at 1 AM
        Schedule::command('orders:process-invoices')->dailyAt('1:00');

    }
}
