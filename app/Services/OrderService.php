<?php

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Models\User;
use App\Repositories\MenuItemRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ReservationRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(protected OrderRepository $orderRepository, protected ReservationRepository $reservationRepository, protected MenuItemRepository $menuItemRepository){}

    public function create(array $data)
    {
        $reservation = $this->reservationRepository->findByField('id', $data['reservation_id']);
        if (!$reservation) {
            throw new Exception("رزرو انجام نشده. لطفا ابتدا میز را رزرو کنید");
        }
        if ($reservation->orders()->exists()) {
            throw new Exception("برای این رزرو قبلاً سفارش ثبت شده است.");
        }
        try {
            DB::beginTransaction();
            $itemIds = collect($data['items'])->pluck('menu_item_id')->toArray();
            $menuItems = $this->menuItemRepository->getByIds($itemIds)->keyBy('id');
            $order = $this->orderRepository->createOrder($reservation->id);

            $total = 0;
            $orderItemsData = [];

            foreach ($data['items'] as $item) {
                $menuItem = $menuItems->get($item['menu_item_id']);
                if (!$menuItem) {
                    throw new OrderException("آیتم با شناسه {$item['menu_item_id']} یافت نشد.", 404);
                }
                if (!$menuItem->is_available) {
                    throw new Exception("آیتم {$menuItem->name} در دسترس نیست.");
                }
                $lineTotal = $menuItem->price * $item['quantity'];
                $total += $lineTotal;
                $orderItemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                ];
            }
            $this->orderRepository->createOrderItems($order, $orderItemsData);
            $this->orderRepository->updateTotalAmount(['total_amount' => $total], $order->id);
            DB::commit();
            return $this->orderRepository->getOrderWithDetails($order->id);
        }catch (Exception $exception){
            DB::rollBack();
            throw $exception;
        }
    }

    public function markAsServed(int $id)
    {
        $order = $this->orderRepository->findByField('id', $id);
        if ($order->status !== 'preparing') {
            throw new OrderException('فقط سفارش در حال آماده‌سازی قابل سرو است');
        }
        $res = $this->orderRepository->updateStatus('served', $order->id);
        return $res;
    }

    public function markAsPaid(int $id)
    {
        $order = $this->orderRepository->findByField('id', $id);
        if ($order->status !== 'served') {
            throw new OrderException('فقط سفارش سرو شده قابل پرداخت است');
        }
        $res = $this->orderRepository->updateStatus('paid', $order->id);
        //TODO:Add flush method after using cache in reports.
        return $res;
    }

    public function cancel(int $id)
    {
        $order = $this->orderRepository->findByField('id', $id);
        if ($order->status === 'paid') {
            throw new OrderException('سفارش پرداخت شده قابل لغو نیست');
        }
        $res = $this->orderRepository->updateStatus('cancelled', $order->id);
        return $res;
    }
}
