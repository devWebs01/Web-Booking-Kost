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
        'check_in_date',
        'check_out_date',
        'customer_name',
        'customer_contact',
        'status',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(payment::class);
    }
}
