<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class OrderService
 *
 * Responsible for handling order placement business rules:
 * - Validate product stock
 * - Calculate totals
 * - Create order + order_items atomically
 * - Reduce stock
 * - Fire domain event OrderPlaced
 * - Order Pay for Changing Status
 */
class OrderService
{
    protected OrderRepository $orders;
    protected ProductRepository $products;

    public function __construct(OrderRepository $orders, ProductRepository $products)
    {
        $this->orders = $orders;
        $this->products = $products;
    }

    /**
     * Create an order transactionally.
     *
     * @param int $buyerId
     * @param array $items
     * @return Order
     * @throws \Throwable when validation/DB fails
     */
    public function createOrder(int $buyerId, array $items): Order
    {
        $order = DB::transaction(function () use ($buyerId, $items) {
            // Get buyer and validate they exist
            $buyer = User::find($buyerId);
            if (!$buyer) {
                throw new InvalidArgumentException("Buyer with ID {$buyerId} not found.");
            }

            // load product info and validate stock
            $total = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $product = $this->products->find($item['product_id']);
                if (!$product) {
                    throw new InvalidArgumentException("Product {$item['product_id']} not found.");
                }
                if ($product->stock_quantity < $item['quantity']) {
                    throw new InvalidArgumentException("Insufficient stock for product ID {$product->id}. Requested: {$item['quantity']}, Available: {$product->stock_quantity}");
                }
                $linePrice = bcmul((string)$product->price, (string)$item['quantity'], 2);
                $total = bcadd((string)$total, (string)$linePrice, 2);

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'seller_id'  => $product->user_id,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price,
                ];
            }

            // Check if buyer has sufficient balance
            if (bccomp((string)$buyer->balance, (string)$total, 2) < 0) {
                throw new InvalidArgumentException("Insufficient balance. Required: {$total}, Available: {$buyer->balance}");
            }

            // create order
            $order = $this->orders->create([
                'buyer_id' => $buyerId,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            // create items
            foreach ($orderItemsData as $row) {
                $order->items()->create($row);
                // reduce stock
                $product = $this->products->find($row['product_id']);
                $this->products->reduceStock($product, $row['quantity']);
            }

            // Deduct amount from buyer's balance
            $buyer->balance = bcsub((string)$buyer->balance, (string)$total, 2);
            $buyer->save();

            return $order;
        }, 5); // 5 attempts for deadlock retry

        // Fire event ONLY after successful transaction commit
        event(new OrderPlaced($order));

        return $order;
    }

    /**
     * Mark an order as paid.
     *
     * @param int $orderId
     * @return \App\Models\Order
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    /**
     * Mark an order as paid by the given user. Only the buyer who placed the order
     * may mark it as paid.
     *
     * @param int $orderId
     * @param \App\Models\User $user
     * @return \App\Models\Order
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    public function markOrderAsPaid(int $orderId, User $user): Order
    {
        $order = $this->orders->find($orderId);

        if (! $order) {
            throw new ModelNotFoundException("Order with ID {$orderId} not found.");
        }

        // Only the buyer who created the order may mark it as paid
        if ($user->id !== $order->buyer_id || $user->role !== 'buyer') {
            throw new InvalidArgumentException('Unauthorized to pay for this order.');
        }

        if ($order->status !== 'pending') {
            throw new InvalidArgumentException("Order {$order->id} is not in pending status.");
        }

        return $this->orders->markAsPaid($order);
    }

    /**
     * List orders scoped for the given user (buyer -> their orders, seller -> their sales).
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listOrdersForUser($user)
    {
        if ($user->role === 'buyer') {
            return $this->orders->getOrdersForBuyer($user->id);
        }

        if ($user->role === 'seller') {
            return $this->orders->getOrdersForSeller($user->id);
        }

        return collect();
    }

    /**
     * Get a single order scoped for the user. Throws ModelNotFoundException if not found.
     *
     * @param int $orderId
     * @param \App\Models\User $user
     * @return \App\Models\Order
     */
    public function getOrderForUser(int $orderId, $user): Order
    {
        $order = $this->orders->findWithItems($orderId);

        if (! $order) {
            throw new ModelNotFoundException("Order with ID {$orderId} not found.");
        }

        // Buyer can access own orders
        if ($user->role === 'buyer' && $order->buyer_id === $user->id) {
            return $order;
        }

        // Seller can access if any item is sold by them
        if ($user->role === 'seller') {
            foreach ($order->items as $item) {
                if ($item->seller_id === $user->id) {
                    return $order;
                }
            }
        }

        throw new InvalidArgumentException('Unauthorized to view this order.');
    }
}
