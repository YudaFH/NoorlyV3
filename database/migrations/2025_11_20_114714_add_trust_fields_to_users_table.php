<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_trusted_creator')->default(false)->after('role');
            $table->unsignedInteger('violation_count')->default(0)->after('is_trusted_creator');
            $table->boolean('is_suspended')->default(false)->after('violation_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_trusted_creator', 'violation_count', 'is_suspended']);
        });
    }
};
