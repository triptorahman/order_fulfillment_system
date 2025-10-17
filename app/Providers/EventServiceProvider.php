<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Fired when a new order is successfully placed
        \App\Events\OrderPlaced::class => [
            \App\Listeners\UpdateSellerBalanceListener::class,
            \App\Listeners\SendOrderConfirmationListener::class,
            \App\Listeners\AuditTrailListener::class,
        ],
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
