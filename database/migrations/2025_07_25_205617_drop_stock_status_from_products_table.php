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
        Schema::table('products', function (Blueprint $table) {
            // Hapus kolom stock_status
            if (Schema::hasColumn('products', 'stock_status')) {
                $table->dropColumn('stock_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Tambal kembali kolom stock_status
            // Pastikan urutan kolom sesuai kebutuhan (misal setelah 'SKU')
            $table->enum('stock_status', ['instock', 'outofstock'])
                ->default('instock')
                ->after('SKU');
        });
    }
};
