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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();    // pembeli
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete(); // kreator
            $table->foreignId('content_id')->constrained()->cascadeOnDelete(); // konten

            $table->unsignedBigInteger('amount')->default(0); // nominal
            $table->string('status', 50)->default('paid');     // 'paid', 'pending', 'failed', 'refunded'
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
