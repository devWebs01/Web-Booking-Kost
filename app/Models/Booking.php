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
        'booking_type',
        'check_in_date',
        'check_out_date',
        'status',
        'price',

        'order_id',
        'snapToken',
        'expired_at',
    ];

    // Sinkronkan waktu dengan middleware AutoCancelBooking
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $setting = Setting::first();
            $expire_time = $setting ? $setting->expire_time : 10; // Default 60 menit jika tidak ada setting

            if (!$booking->expired_at) {
                $booking->expired_at = now()->addMinutes($expire_time);
            }
        });
    }

    public function getTimeRemainingAttribute()
    {
        return $this->expired_at ? max(0, $this->expired_at->diffInSeconds(now())) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
