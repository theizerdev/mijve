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
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->dateTime('fecha_emision');
            $table->string('serie');
            $table->string('tipo'); // factura, boleta, recibo, etc.
            $table->json('contenido');
            $table->morphs('comprobanteable');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
