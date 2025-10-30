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
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->date('fecha_pago');
            $table->string('metodo_pago')->nullable();
            $table->string('referencia')->nullable();
            $table->string('estado')->default('pendiente');
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
