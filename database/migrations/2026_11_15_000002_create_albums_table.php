<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('artist');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->date('release_date')->nullable();
            $table->string('genre')->nullable();
            $table->string('label')->nullable();
            $table->decimal('price_buy', 12, 2);
            $table->decimal('price_rent', 12, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
