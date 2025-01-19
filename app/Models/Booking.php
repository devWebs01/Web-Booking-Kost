<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'check_in_date',
        'check_out_date',
        'totalRooms',
        'customer_name',
        'customer_contact',
        'status',
        'type',
        'order_id', // Menambahkan order_id ke dalam fillable

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

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }
}
