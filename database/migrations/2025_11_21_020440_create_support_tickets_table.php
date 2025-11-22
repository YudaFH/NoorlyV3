<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('category', 100);        // pembayaran, penarikan, bug, dsb
            $table->string('subject', 191);
            $table->text('message');

            // open | in_progress | resolved | closed
            $table->string('status', 50)->default('open');

            // path file lampiran (opsional)
            $table->string('attachment_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
