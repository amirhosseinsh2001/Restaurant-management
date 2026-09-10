<?php

namespace App\Repositories;

use App\Models\Report;
use App\Repositories\Core\CoreRepository;
use Illuminate\Support\Facades\DB;

class ReportRepository extends CoreRepository
{
    public function __construct(Report $model)
    {
        parent::__construct($model);
    }
    //TODO: Add cache to reports.
    public function bestDesksReport(string $startTime, string $endTime)
    {
        return DB::table('orders')
            ->join('reservations', 'orders.reservation_id', '=', 'reservations.id')
            ->join('desks', 'reservations.desk_id', '=', 'desks.id')

            ->whereBetween('orders.created_at', [$startTime, $endTime])
//            ->where('orders.status', 'paid')
            ->groupBy('desks.id', 'desks.desk_number')
            ->select(
                'desks.id',
                'desks.desk_number',
                DB::raw('SUM(orders.total_amount) as total_revenue'),
                DB::raw('COUNT(orders.id) as orders_count')
            )
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();
    }

    public function bestMenuItemsReport(string $startTime, string $endTime)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')

            ->whereBetween('orders.created_at', [$startTime, $endTime])
//            ->where('orders.status', 'paid')

            ->groupBy('menu_items.id', 'menu_items.name')

            ->select(
                'menu_items.id',
                'menu_items.name',
                DB::raw('SUM(order_items.quantity) as total_sold_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue')
            )

            ->orderByDesc('total_sold_quantity')
//            ->orderByDesc('total_revenue')
            ->limit(10)

            ->get();
    }
}
