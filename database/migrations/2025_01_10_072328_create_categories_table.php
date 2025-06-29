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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            // nama & slug unik global
            $table->string('name');
            $table->string('slug')->unique();

            // gambar opsional
            $table->string('image')->nullable();

            // self-reference untuk parent/child
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();

            // foreign key ke diri sendiri
            $table->foreign('parent_id')
                ->references('id')->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
