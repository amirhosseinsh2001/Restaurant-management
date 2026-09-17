<?php

namespace App\Models;

use Database\Factories\DeskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    /** @use HasFactory<DeskFactory> */
    use HasFactory;
    protected $fillable = ['desk_number', 'capacity', 'status'];

//    public function orders()
//    {
//        return $this->hasMany(Order::class);
//    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function scopeAvailableBetween($query, $date, $start, $end)
    {
        return $query->whereDoesntHave('reservations', function ($q) use ($date, $start, $end) {
            $q->where('reservation_date', $date)
                ->where('start_time', '<', $end)
                ->where('end_time', '>', $start);
        });
    }
}
