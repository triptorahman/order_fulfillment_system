<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AuditTrailListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderPlaced $event)
    {
        $order = $event->order;

        // Load order items with relationships for complete audit info
        $order->load('items.product', 'buyer');

        $auditData = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'buyer_id' => $order->buyer_id,
            'buyer_name' => $order->buyer->name,
            'buyer_email' => $order->buyer->email,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'items_count' => $order->items->count(),
            'items' => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'seller_id' => $item->seller_id,
                ];
            }),
            'timestamp' => now()->toDateTimeString(),
        ];

        // Log to the dedicated orders log channel
        Log::channel('orders')->info('Order placed', $auditData);
    }
}
