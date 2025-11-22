<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('title');
            $table->string('slug')->unique();

            // ebook, video, webinar, template, dll
            $table->string('type')->nullable();

            // draft, published, scheduled, pending_review, archived
            $table->string('status')->default('draft');

            $table->unsignedInteger('price')->default(0);

            // Metrik performa
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('buyers_count')->default(0);
            $table->unsignedBigInteger('revenue_total')->default(0);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
