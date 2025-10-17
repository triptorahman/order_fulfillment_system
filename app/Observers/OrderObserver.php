<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderObserver
{
    public function creating(Order $order)
    {
        // ensure unique order_number
        $order->order_number = 'ORD-' . strtoupper(Str::random(10)) . '-' . now()->format('Ymd');
    }
}
