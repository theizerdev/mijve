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
        Schema::table('pagos', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos', 'caja_id')) {
                $table->unsignedBigInteger('caja_id')->nullable()->after('sucursal_id');
                $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            if (Schema::hasColumn('pagos', 'caja_id')) {
                $table->dropForeign(['caja_id']);
                $table->dropColumn('caja_id');
            }
        });
    }
};
