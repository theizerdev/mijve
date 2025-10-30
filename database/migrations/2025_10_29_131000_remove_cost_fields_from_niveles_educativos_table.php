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
        Schema::table('niveles_educativos', function (Blueprint $table) {
            $table->dropColumn(['costo_matricula', 'costo_mensualidad', 'costo', 'cuota_inicial', 'numero_cuotas']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('niveles_educativos', function (Blueprint $table) {
            $table->decimal('costo', 10, 2)->default(0)->after('descripcion');
            $table->decimal('cuota_inicial', 10, 2)->default(0)->after('costo');
            $table->integer('numero_cuotas')->default(0)->after('cuota_inicial');
            $table->decimal('costo_matricula', 10, 2)->default(0)->after('numero_cuotas');
            $table->decimal('costo_mensualidad', 10, 2)->default(0)->after('costo_matricula');
        });
    }
};