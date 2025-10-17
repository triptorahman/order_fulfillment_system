<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Gate;
use App\Models\Order;
use App\Observers\OrderObserver;
use App\Policies\OrderPolicy;

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
        // Register Order policy so we can use $this->authorize / Gate checks
        Gate::policy(Order::class, OrderPolicy::class);
        // Run invoice processing once per day at 1 AM
        Schedule::command('orders:process-invoices')->dailyAt('1:00');

    }
}
