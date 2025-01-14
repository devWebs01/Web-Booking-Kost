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

    public function bookings(): HasMany
    {
        return $this->hasMany(booking::class);
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

  
}
