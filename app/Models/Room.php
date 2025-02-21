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
        'room_status',
        'position',
    ];

    /**
     * Get all of the carts for the Room
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
