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
            $table->decimal('costo', 10, 2)->default(0)->after('periodo_id');
            $table->decimal('cuota_inicial', 10, 2)->default(0)->after('costo');
            $table->integer('numero_cuotas')->default(0)->after('cuota_inicial');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            $table->dropColumn(['costo', 'cuota_inicial', 'numero_cuotas']);
        });
    }
};