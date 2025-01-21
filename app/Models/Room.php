<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'daily_price',
        'monthly_price',
        'room_status',
    ];

    /**
     * Get all of the facilities for the Room
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Get all of the images for the Room
     */
    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    /**
     * Get all of the carts for the Room
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}
