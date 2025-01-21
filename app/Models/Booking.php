<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'order_id',
        'total',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->order_id = self::generateOrderId();
        });
    }

    public static function generateOrderId()
    {
        $timestamp = now()->format('YmdHis'); // Format waktu sebagai bagian dari ID
        $randomNumber = mt_rand(1000, 9999); // Tambahkan angka acak untuk memastikan keunikan

        return "INV-{$timestamp}-{$randomNumber}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get all of the items for the Booking
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
