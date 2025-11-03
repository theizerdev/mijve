<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_documento'); // factura, boleta, nota_credito, recibo
            $table->string('serie', 10); // F001, B001, NC01, R001
            $table->integer('correlativo_actual')->default(0);
            $table->integer('longitud_correlativo')->default(8); // Padding del número
            $table->boolean('activo')->default(true);
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['serie', 'empresa_id', 'sucursal_id']);
            $table->index(['tipo_documento', 'empresa_id', 'sucursal_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
