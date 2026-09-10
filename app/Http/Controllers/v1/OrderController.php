<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\createOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService){}

    public function index()
    {

    }
    public function store(createOrderRequest $request)
    {
        $data = $request->validated();
        $result = $this->orderService->create($data);
        return $this->successResponse($result, __('messages.orders.order_created'), 200);
    }

    public function serve($id)
    {
        $result = $this->orderService->markAsServed($id);
        return $this->successResponse($result, 'سفارش سرو شد', 200);
    }

    public function pay($id)
    {
        $result = $this->orderService->markAsPaid($id);
        return $this->successResponse($result, 'سفارش پرداخت شد', 200);
    }

    public function cancel($id)
    {
        $result = $this->orderService->cancel($id);
        return $this->successResponse($result, 'سفارش لغو شد', 200);
    }
}
