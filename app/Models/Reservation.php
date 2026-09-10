<?php

namespace App\Models;

use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id', 'desk_id', 'reservation_date', 'start_time', 'end_time', 'guests_count', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function desk()
    {
        return $this->belongsTo(Desk::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
