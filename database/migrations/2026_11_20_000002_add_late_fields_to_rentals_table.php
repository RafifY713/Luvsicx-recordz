<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->integer('late_days')->default(0)->after('status');
            $table->decimal('late_fee', 12, 2)->default(0)->after('late_days');
            $table->boolean('late_fee_paid')->default(false)->after('late_fee');
            $table->timestamp('late_fee_paid_at')->nullable()->after('late_fee_paid');
            $table->string('late_fee_proof')->nullable()->after('late_fee_paid_at');
            $table->timestamp('late_fee_proof_uploaded_at')->nullable()->after('late_fee_proof');
            $table->timestamp('late_fee_verified_at')->nullable()->after('late_fee_proof_uploaded_at');
            $table->foreignId('late_fee_verified_by')->nullable()
                ->after('late_fee_verified_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('late_fee_verified_by');
            $table->dropColumn([
                'late_days',
                'late_fee',
                'late_fee_paid',
                'late_fee_paid_at',
                'late_fee_proof',
                'late_fee_proof_uploaded_at',
                'late_fee_verified_at',
            ]);
        });
    }
};
