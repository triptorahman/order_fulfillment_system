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
}
