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
