<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_price',
        'monthly_price',
        'description',
        'room_status',
    ];

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Get all of the bookingRooms for the Booking
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }
}
