<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_id',
        'image_path',
    ];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
