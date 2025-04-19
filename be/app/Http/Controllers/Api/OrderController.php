<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_telepon' => 'required|string|max:15',
            'alamat' => 'nullable|string',
            'metode_pembayaran' => 'required|in:transfer,tempo',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        $tanggalJatuhTempo = null;
        if ($validatedData['metode_pembayaran'] === 'tempo') {
            $tanggalJatuhTempo = Carbon::now()->addDays(7);
        }

          // Buat order baru
          $order = new Order();
          $order->nama_pelanggan = $validatedData['nama_pelanggan'];
          $order->nomor_telepon = $validatedData['nomor_telepon'];
          $order->alamat = $validatedData['alamat'] ?? null;
          $order->metode_pembayaran = $validatedData['metode_pembayaran'];
          $order->status_pembayaran = $validatedData['metode_pembayaran'] === 'transfer' ? 'pending' : 'belum_lunas';
          $order->tanggal_order = Carbon::now();
          $order->tanggal_jatuh_tempo = $tanggalJatuhTempo;
          $order->catatan = $validatedData['catatan'] ?? null;

          // Hitung total
        $total = 0;
        foreach ($validatedData['items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $subtotal = $menu->harga * $item['jumlah'];
            $total += $subtotal;
        }

        $order->total = $total;
        $order->save();

        // Simpan item order
        foreach ($validatedData['items'] as $item) {
            $menu = Menu::findOrFail($item['menu_id']);
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->menu_id = $item['menu_id'];
            $orderItem->jumlah = $item['jumlah'];
            $orderItem->harga_satuan = $menu->harga;
            $orderItem->subtotal = $menu->harga * $item['jumlah'];
            $orderItem->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dibuat',
            'order' => [
                'id' => $order->id,
                'total' => $order->total,
                'metode_pembayaran' => $order->metode_pembayaran,
            ],
        ], 201);
    }
}
