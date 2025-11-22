<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();

            // Kreator yang menarik saldo
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Nominal penarikan (wajib)
            $table->unsignedBigInteger('amount');

            // Status: pending, approved, paid, rejected
            $table->string('status', 20)->default('pending');

            // Info rekening tujuan (optional, bisa disesuaikan)
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();

            // Catatan tambahan dari kreator / admin
            $table->text('notes')->nullable();

            // Tanggal benar-benar dibayar (opsional)
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraws');
    }
};
