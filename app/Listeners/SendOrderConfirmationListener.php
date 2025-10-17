<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class SendOrderConfirmationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderPlaced $event)
    {
        $order = $event->order;

        // Load necessary relationships for the email template
        $order->load('buyer', 'items.product', 'items.seller');

        // Render the blade template as HTML
        $emailContent = View::make('emails.order-confirmation', [
            'order' => $order
        ])->render();

        // Log the simulated email to simulate_email.log
        Log::channel('simulate_email')->info('Order Confirmation Email', [
            'to' => $order->buyer->email,
            'to_name' => $order->buyer->name,
            'subject' => "Order Confirmation - {$order->order_number}",
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total_amount' => $order->total_amount,
            'timestamp' => now()->toDateTimeString(),
            'html_content' => $emailContent
        ]);

        // Notify in main log
        Log::info("Order confirmation email simulated", [
            'order_id' => $order->id,
            'buyer_email' => $order->buyer->email
        ]);
    }
}
