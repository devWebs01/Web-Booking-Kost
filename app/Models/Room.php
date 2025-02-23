<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'room_status',
        'position',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
