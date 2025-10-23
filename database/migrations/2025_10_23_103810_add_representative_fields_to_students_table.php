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
        Schema::table('students', function (Blueprint $table) {
            // Campos para representante (no obligatorios)
            $table->string('representante_nombres')->nullable();
            $table->string('representante_apellidos')->nullable();
            $table->string('representante_documento_identidad')->nullable();
            $table->string('representante_telefonos')->nullable(); // Se almacenarán múltiples teléfonos separados por coma
            $table->string('representante_correo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'representante_nombres',
                'representante_apellidos',
                'representante_documento_identidad',
                'representante_telefonos',
                'representante_correo'
            ]);
        });
    }
};