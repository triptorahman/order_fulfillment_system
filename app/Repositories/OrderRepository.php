<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-16

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    protected Order $model;
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Order
    {
        return $this->model->create($data);
    }

    public function find(int $id): ?Order
    {
        return $this->model->find($id);
    }

    /**
     * Mark a specific order as paid.
     *
     * @param \App\Models\Order $order
     * @return \App\Models\Order
     */
    public function markAsPaid(Order $order): Order
    {
        $order->status = 'paid';
        $order->paid_at = now();
        $order->save();

        return $order;
    }

    public function findUninvoicedPaidOrders()
    {
        return $this->model->where('status', 'paid')->whereNull('invoiced_at')->get();
    }

    /**
     * Find an order and eager load its items.
     *
     * @param int $id
     * @return \App\Models\Order|null
     */
    public function findWithItems(int $id): ?Order
    {
        return $this->model->with('items')->find($id);
    }

    /**
     * Get orders belonging to a buyer.
     *
     * @param int $buyerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersForBuyer(int $buyerId)
    {
        return $this->model->where('buyer_id', $buyerId)->with('items')->get();
    }

    /**
     * Get orders that include items sold by the given seller.
     *
     * @param int $sellerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrdersForSeller(int $sellerId)
    {
        return $this->model->whereHas('items', function ($q) use ($sellerId) {
            $q->where('seller_id', $sellerId);
        })->with('items')->get();
    }

    /**
     * Mark a specific order as paid.
     *
     * @param \App\Models\Order $order
     * @return void
     */
    public function markInvoiced(Order $order)
    {
        $order->status = 'invoiced';
        $order->invoiced_at = now();
        $order->save();
    }
}
