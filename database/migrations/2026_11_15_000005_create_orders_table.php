<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['qris', 'cod']);
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['pending', 'accepted', 'completed', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
