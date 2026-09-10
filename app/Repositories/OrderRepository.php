<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Core\CoreRepository;
use Illuminate\Database\Eloquent\Model;

class OrderRepository extends CoreRepository
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function createOrder(int $reservationId)
    {
        return $this->model->create([
            'reservation_id' => $reservationId,
            'total_amount' => 0,
            'status' => 'preparing'
        ]);
    }

    public function createOrderItems($order, array $itemsData)
    {
        return $order->orderItems()->createMany($itemsData);
    }

    public function getOrderWithDetails(int $orderId)
    {
        return $this->model->with('orderItems.menuItem')->find($orderId);
    }

    public function updateTotalAmount(array $data, int $id)
    {
        $order = $this->findByField('id', $id);
        return $order->update($data);
//        return $order->refresh();
    }

    public function updateStatus(string $data, int $id)
    {
        $order = $this->findByField('id', $id);
        $order->update(['status' => $data]);
        return $order->refresh();
    }
}
