<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'deskripsi',
        'tersedia',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    } 
}

