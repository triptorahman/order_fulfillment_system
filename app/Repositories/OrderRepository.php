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

}
