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
}
