<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // balance, content, buyer, support, system
            $table->string('type', 50);

            $table->string('title', 191);
            $table->text('body')->nullable();

            // data tambahan (json) misal: url, content_id, withdraw_id, dsb
            $table->json('data')->nullable();

            // null = belum dibaca
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
