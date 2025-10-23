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
        // Verificamos si las columnas ya existen antes de intentar crearlas
        Schema::table('niveles_educativos', function (Blueprint $table) {
            if (!Schema::hasColumn('niveles_educativos', 'costo')) {
                $table->decimal('costo', 10, 2)->default(0)->after('descripcion');
            }
            if (!Schema::hasColumn('niveles_educativos', 'cuota_inicial')) {
                $table->decimal('cuota_inicial', 10, 2)->default(0)->after('costo');
            }
            if (!Schema::hasColumn('niveles_educativos', 'numero_cuotas')) {
                $table->integer('numero_cuotas')->default(0)->after('cuota_inicial');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('niveles_educativos', function (Blueprint $table) {
            if (Schema::hasColumn('niveles_educativos', 'costo')) {
                $table->dropColumn('costo');
            }
            if (Schema::hasColumn('niveles_educativos', 'cuota_inicial')) {
                $table->dropColumn('cuota_inicial');
            }
            if (Schema::hasColumn('niveles_educativos', 'numero_cuotas')) {
                $table->dropColumn('numero_cuotas');
            }
        });
    }
};
