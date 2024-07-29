<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'room_id',
        'status',
        'durability',
        'member',
        'created_by',
        'updated_by',
    ];

    public function room(){
        return $this->belongsTo(Room::class,'room_id');
    }
}
