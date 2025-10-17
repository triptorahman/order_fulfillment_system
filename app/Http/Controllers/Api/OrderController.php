<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use Illuminate\Http\Request;
use App\Services\OrderService;
use InvalidArgumentException;
use Exception;

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
        try {
            $data = $request->validated();

            $order = $this->service->createOrder($data['buyer_id'], $data['items']);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => [
                    'order' => $order
                ]
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order creation failed',
                'error' => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
                'error' => 'Please try again later or contact support'
            ], 500);
        }
    }

    /**
     * Mark an order as paid.
     *
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse
     *
     * @example
     * POST /api/orders/{id}/pay
     */
    public function pay(int $orderId)
    {
        try {
            $order = $this->service->markOrderAsPaid($orderId);

            return response()->json([
                'message' => 'Order marked as paid successfully.',
                'order' => $order,
            ], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
