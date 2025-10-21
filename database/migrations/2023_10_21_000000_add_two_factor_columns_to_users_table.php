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
            $table->text('two_factor_secret')
                ->after('remember_token')
                ->nullable();

            $table->json('two_factor_recovery_codes')
                ->after('two_factor_secret')
                ->nullable();

            $table->boolean('two_factor_enabled')
                ->after('two_factor_recovery_codes')
                ->default(false);

            $table->timestamp('verification_code_sent_at')
                ->after('two_factor_enabled')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'two_factor_secret',
                'two_factor_recovery_codes',
                'two_factor_enabled',
                'verification_code_sent_at'
            ]);
        });
    }
};
