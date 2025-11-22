<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->enum('type', ['bank', 'ewallet']);
            $table->string('provider_code');   // BCA, BNI, DANA, OVO, dll
            $table->string('provider_name');   // "BCA", "DANA", dll

            $table->string('account_name');    // yuda
            $table->string('account_number');  // 8330243482

            $table->boolean('is_default')->default(false);

            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->text('status_note')->nullable();      // catatan admin kalau ditolak

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_methods');
    }
};
