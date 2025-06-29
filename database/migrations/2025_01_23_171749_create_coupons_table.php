<?php

use Illuminate\Support\Facades\DB;
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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // 1. referensi ke toko
            $table->unsignedBigInteger('store_id')
                ->comment('Referensi ke stores.id');

            // 2. kode kupon tanpa unique global
            $table->string('code');

            // 3. tipe dan nilai
            $table->enum('type', ['fixed', 'percent']);
            $table->decimal('value', 10, 2);
            $table->decimal('cart_value', 10, 2);

            $table->timestamp('expiry_date')->useCurrent();

            $table->timestamps();

            // --- constraints & index ---

            // slug/code unik per toko
            $table->unique(['store_id', 'code']);

            // foreign‐key ke stores
            $table->foreign('store_id')
                ->references('id')->on('stores')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
