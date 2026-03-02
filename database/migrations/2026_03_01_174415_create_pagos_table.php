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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('participante_id')->constrained('participantes')->onDelete('cascade');
            $table->foreignId('actividad_id')->constrained('actividads')->onDelete('cascade');
            $table->foreignId('metodo_pago_id')->constrained('metodo_pagos')->onDelete('cascade');
            
            $table->decimal('monto_euro', 10, 2);
            $table->decimal('tasa_cambio', 10, 4);
            $table->decimal('monto_bolivares', 10, 2);
            
            $table->date('fecha_pago');
            $table->string('referencia_bancaria')->nullable();
            $table->string('evidencia_pago')->nullable(); // Path al archivo
            
            $table->enum('status', ['Pendiente', 'Aprobado', 'Rechazado'])->default('Pendiente');
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
