<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('album_id')->constrained();
            $table->enum('type', ['buy', 'rent']);
            $table->integer('quantity');
            $table->integer('rental_days')->nullable();
            $table->date('rental_start_date')->nullable();
            $table->date('rental_end_date')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
