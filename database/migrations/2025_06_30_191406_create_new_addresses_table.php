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
        Schema::create('new_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()           // references `id` on `users`
                ->onDelete('cascade');

            $table->integer('destination_id');      // ID Alamat
            $table->string('province');         // Provinsi
            $table->string('city');             // Kota/Kabupaten
            $table->string('district');         // Kecamatan
            $table->string('subdistrict');      // Kelurahan/Desa
            $table->string('full_address');     // Alamat Lengkap

            $table->string('zip_code');              // Kode pos
            $table->string('phone');                 // Nomor telepon
            $table->string('recipient_name');        // Nama Penerima

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_addresses');
    }
};
