<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pelanggan',
        'nomor_telepon',
        'alamat',
        'metode_pembayaran', // transfer atau tempo
        'status_pembayaran', // pending, lunas, belum_lunas
        'tanggal_order',
        'tanggal_jatuh_tempo',
        'total',
        'catatan',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
