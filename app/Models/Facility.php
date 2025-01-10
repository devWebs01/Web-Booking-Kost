<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        // 'description',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(room::class);
    }
}
