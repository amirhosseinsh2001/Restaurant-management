<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;
    protected $fillable = ['reservation_id', 'total_amount', 'status'];

//    public function desk()
//    {
//        return $this->belongsTo(Desk::class);
//    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
