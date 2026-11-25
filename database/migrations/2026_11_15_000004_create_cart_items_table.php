<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->enum('type', ['buy', 'rent'])->default('buy');
            $table->integer('rental_days')->nullable();
            $table->date('rental_start_date')->nullable();
            $table->date('rental_end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
