<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;
    protected $table = 'rooms';
    protected $fillable = ['hotel_id', 'number', 'type', 'price'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
