<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_id',
        'name',
    ];

    /**
     * Get the room that owns the Facility
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
