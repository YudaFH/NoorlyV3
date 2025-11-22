<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->string('cover_path')->nullable()->after('price');

            // File utama (ebook pdf, template, video lokal, dll)
            $table->string('primary_file_path')->nullable()->after('cover_path');

            // Link utama (YouTube, Google Drive, Zoom recording, dsb)
            $table->string('primary_link_url')->nullable()->after('primary_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['cover_path', 'primary_file_path', 'primary_link_url']);
        });
    }
};
