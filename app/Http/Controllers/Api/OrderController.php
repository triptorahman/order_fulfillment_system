<?php
// Developer: Md Samiur Rahman | Reviewed: 2025-10-17

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderRequest;
use App\Services\OrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use InvalidArgumentException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    use AuthorizesRequests;
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
     * List orders for the authenticated user.
     * Buyers see their orders; sellers see orders containing their sales.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $orders = $this->service->listOrdersForUser($user);

        return response()->json(['data' => $orders], 200);
    }

    /**
     * Show a single order if authorized.
     */
    public function show(int $orderId)
    {
        $user = Auth::user();

        try {
            $order = $this->service->getOrderForUser($orderId, $user);
            return response()->json(['data' => $order], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Order not found'], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'Unauthorized'], 403);
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
        $user = Auth::user();

        try {
            $order = $this->service->markOrderAsPaid($orderId, $user);

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
