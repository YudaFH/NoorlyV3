<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();

            // relasi ke tabel support_tickets
            $table->foreignId('support_ticket_id')
                ->constrained('support_tickets')
                ->onDelete('cascade');

            // pengirim pesan (bisa user atau admin)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // siapa pengirimnya
            $table->string('sender_type')->default('user');
            // bisa juga pakai enum kalau mau:
            // $table->enum('sender_type', ['user', 'admin', 'system'])->default('user');

            $table->text('message');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
    }
};
