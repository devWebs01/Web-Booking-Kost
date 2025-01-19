<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'booking_id',
    ];

    /**
     * Get the room that owns the BookingRoom
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the booking that owns the BookingRoom
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
