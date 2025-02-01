<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'gross_amount',
        'payment_time',
        'payment_type',
        'status',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(booking::class);
    }
}
