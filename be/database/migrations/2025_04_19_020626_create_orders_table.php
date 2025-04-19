<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('nomor_telepon');
            $table->text('alamat')->nullable();
            $table->enum('metode_pembayaran', ['transfer', 'tempo']);
            $table->enum('status_pembayaran', ['pending', 'lunas', 'belum_lunas'])->default('pending');
            $table->date('tanggal_order');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('total', 10, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
