<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // role: user biasa atau creator
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')
                      ->default('user')
                      ->after('id');
            }

            // nomor telepon & verifikasi (opsional tapi sangat berguna untuk OTP)
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'phone_verified')) {
                $table->boolean('phone_verified')->default(false)->after('phone');
            }

            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone_verified');
            }

            // profil kreator (hanya terpakai jika role = creator)
            if (! Schema::hasColumn('users', 'creator_name')) {
                $table->string('creator_name')->nullable()->after('name');
            }

            if (! Schema::hasColumn('users', 'main_content_type')) {
                $table->string('main_content_type')->nullable()->after('creator_name');
            }

            // untuk login Google (opsional, tapi sekalian disiapkan)
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // down() boleh tetap drop, tapi cek dulu biar aman
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'phone_verified')) {
                $table->dropColumn('phone_verified');
            }
            if (Schema::hasColumn('users', 'phone_verified_at')) {
                $table->dropColumn('phone_verified_at');
            }
            if (Schema::hasColumn('users', 'creator_name')) {
                $table->dropColumn('creator_name');
            }
            if (Schema::hasColumn('users', 'main_content_type')) {
                $table->dropColumn('main_content_type');
            }
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropColumn('google_id');
            }
        });
    }
};
