<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateSellerBalanceListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderPlaced $event)
    {
        $order = $event->order;

        // Load the order items with seller relationship to avoid N+1 queries
        $order->load('items.seller');

        foreach ($order->items as $item) {
            $seller = $item->seller;

            if (!$seller) {
                Log::error("Seller not found for order item", [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                    'seller_id' => $item->seller_id
                ]);
                continue;
            }

            // Calculate amount to add to seller balance
            $amount = bcmul((string)$item->price, (string)$item->quantity, 2);

            // Add to seller balance
            $seller->balance = bcadd((string)$seller->balance, $amount, 2);
            $seller->save();

            Log::info("Seller balance updated", [
                'seller_id' => $seller->id,
                'order_id' => $order->id,
                'amount_added' => $amount,
                'new_balance' => $seller->balance
            ]);
        }
    }
}
