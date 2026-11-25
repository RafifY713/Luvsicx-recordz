<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_proof')->nullable()->after('paid_at');
            $table->timestamp('payment_proof_uploaded_at')->nullable()->after('payment_proof');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_proof_uploaded_at');
            $table->foreignId('payment_verified_by')->nullable()
                ->after('payment_verified_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->dropColumn('paid_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('paid_amount', 12, 2)->nullable()->after('payment_status');

            $table->dropConstrainedForeignId('payment_verified_by');
            $table->dropColumn([
                'payment_proof',
                'payment_proof_uploaded_at',
                'payment_verified_at',
            ]);
        });
    }
};
