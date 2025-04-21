<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read float $total
 * @property int $id
 * @property int $user_id
 * @property string $booking_type
 * @property string $check_in_date
 * @property string $check_out_date
 * @property string $price
 * @property string $status
 * @property string|null $order_id
 * @property string|null $snapToken
 * @property string $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int|null $time_remaining
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereBookingType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereSnapToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUserId($value)
 *
 * @mixin \Eloquent
 */
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

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($booking): void {
            $setting = Setting::first();
            $expire_time = $setting ? $setting->expire_time : 10;

            if (! $booking->expired_at) {
                $booking->expired_at = now()->addMinutes($expire_time);
            }
        });
    }

    public function getTimeRemainingAttribute(): ?int
    {
        if ($this->expired_at) {
            $expiredAt = Carbon::parse($this->expired_at);

            return max(0, $expiredAt->diffInSeconds(now()));
        }

        return null;
    }

    public function getTotalAttribute(): mixed
    {
        return $this->items->sum(fn ($item) => $item->price);
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
