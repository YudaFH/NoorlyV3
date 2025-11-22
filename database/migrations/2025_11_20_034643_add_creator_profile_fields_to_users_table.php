<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profil kreator
            $table->string('bio', 500)->nullable()->after('email');
            $table->string('avatar_path')->nullable()->after('bio');

            // Metode penarikan
            $table->string('withdrawal_type')->nullable()->after('avatar_path'); // bank | ewallet

            // Rekening bank
            $table->string('bank_name')->nullable()->after('withdrawal_type');
            $table->string('bank_account_name')->nullable()->after('bank_name');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');

            // E-wallet
            $table->string('ewallet_provider')->nullable()->after('bank_account_number');
            $table->string('ewallet_number')->nullable()->after('ewallet_provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'avatar_path',
                'withdrawal_type',
                'bank_name',
                'bank_account_name',
                'bank_account_number',
                'ewallet_provider',
                'ewallet_number',
            ]);
        });
    }
};
