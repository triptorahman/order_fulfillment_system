<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected OrderService $service;

    public function __construct(OrderService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a new order
     *
     * @param  OrderRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        $order = $this->service->createOrder($data['buyer_id'], $data['items']);

        return response()->json(['order' => $order], 201);
    }
}
