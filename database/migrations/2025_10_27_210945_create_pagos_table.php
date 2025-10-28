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
            $table->foreignId('matricula_id')->constrained('matriculas');
            $table->foreignId('concepto_pago_id')->constrained('conceptos_pago');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->date('fecha_pago');
            $table->string('metodo_pago')->nullable(); // efectivo, tarjeta, transferencia, etc.
            $table->string('referencia')->nullable(); // número de recibo, referencia bancaria, etc.
            $table->string('estado')->default('pendiente'); // pendiente, parcial, completo
            $table->timestamps();
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