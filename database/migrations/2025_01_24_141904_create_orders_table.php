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

            // 1. Toko & User
            $table->foreignId('store_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // 2. Ringkasan harga
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);

            // 3. Pilihan kurir & ongkir
            $table->string('shipping_service')
                ->comment('kode layanan kurir, e.g. "jne_reg", "jnt_oke"');
            $table->decimal('shipping_cost', 10, 2)
                ->comment('biaya kirim sesuai layanan');

            // 4. Data alamat pengiriman
            $table->unsignedBigInteger('destination_id')
                ->comment('ID desa/kelurahan dari API alamat');
            $table->string('province');
            $table->string('city');
            $table->string('district');
            $table->string('subdistrict');
            $table->text('full_address');
            $table->string('zip_code');
            $table->string('phone');
            $table->string('recipient_name');

            // 5. Status & tanggal sesuai workflow:
            //    ordered → shipped → delivered → completed (opsional) → canceled
            $table->enum('status', ['ordered', 'shipped', 'delivered', 'completed', 'canceled'])
                ->default('ordered');
            $table->boolean('is_shipping_different')
                ->default(false);

            // Tanggal pencapaian tiap status penting
            $table->dateTime('shipped_at')->nullable()
                ->comment('waktu ketika kurir mengambil paket');
            $table->dateTime('delivered_at')->nullable()
                ->comment('waktu paket sampai di customer');
            $table->dateTime('completed_at')->nullable()
                ->comment('waktu order secara administrasi ditutup');
            $table->dateTime('canceled_at')->nullable()
                ->comment('waktu order dibatalkan');

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
