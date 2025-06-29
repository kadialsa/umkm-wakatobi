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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // referensi ke order (yang sudah punya store_id & user_id)
            $table->unsignedBigInteger('order_id');

            // metode pembayaran umum di Indonesia
            $table->enum('mode', [
                'cod',
                'bank_transfer',
                'gopay',
                'ovo',
                'dana',
                'shopeepay',
            ])->default('bank_transfer');

            // status transaksi
            $table->enum('status', [
                'pending',
                'approved',
                'declined',
                'refunded',
            ])->default('pending');

            $table->timestamps();

            // foreign key
            $table->foreign('order_id')
                ->references('id')->on('orders')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
