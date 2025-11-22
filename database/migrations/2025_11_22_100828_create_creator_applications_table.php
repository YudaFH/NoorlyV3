<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creator_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('full_name');
            $table->string('tagline')->nullable();            // tagline singkat
            $table->string('niche')->nullable();              // niche utama
            $table->string('experience_level')->nullable();   // pemula / menengah / pro

            $table->string('content_types')->nullable();      // kelas online, ebook, template, dll (disimpan string)

            // sosial media / kontak
            $table->string('social_instagram')->nullable();
            $table->string('social_tiktok')->nullable();
            $table->string('social_youtube')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('phone')->nullable();

            $table->text('about')->nullable();                // deskripsi diri / tentang kreator

            $table->string('status')->default('pending');     // pending, approved, rejected
            $table->text('admin_notes')->nullable();          // catatan admin

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_applications');
    }
};
