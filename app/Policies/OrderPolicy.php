<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/**
 * Policy that controls access to orders.
 * - Buyers may view their own orders
 * - Sellers may view orders that contain items sold by them
 */
class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // Buyer can view their own order
        if ($user->id === $order->buyer_id && $user->role === 'buyer') {
            return true;
        }

        // Seller can view if any item belongs to them
        if ($user->role === 'seller') {
            foreach ($order->items as $item) {
                if ($item->seller_id === $user->id) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can view order list.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated buyers/sellers may request a list; scoping is done in controllers
        return in_array($user->role, ['buyer', 'seller']);
    }
}
