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
        Schema::table('participantes', function (Blueprint $table) {
            $table->foreignId('extension_id')->nullable()->after('sucursal_id')->constrained('extensiones')->onDelete('set null');
            
            // Si ya existen columnas zona y distrito, las mantenemos pero pueden ser nullable
            // Si no existen, las agregamos para mantener compatibilidad si se requieren guardar como histórico
            if (!Schema::hasColumn('participantes', 'zona')) {
                $table->string('zona')->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('participantes', 'distrito')) {
                $table->string('distrito')->nullable()->after('zona');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropForeign(['extension_id']);
            $table->dropColumn('extension_id');
            // No eliminamos zona y distrito en rollback para no perder datos si ya existían antes
        });
    }
};
