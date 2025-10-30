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
        Schema::table('matriculas', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->after('id');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->after('empresa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['empresa_id', 'sucursal_id']);
        });
    }
};
