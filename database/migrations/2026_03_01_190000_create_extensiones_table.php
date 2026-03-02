<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extensiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('cascade');
            $table->string('nombre');
            $table->string('zona')->nullable();
            $table->string('distrito')->nullable();
            $table->enum('status', ['Activo', 'Inactivo', 'Pendiente'])->default('Activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extensiones');
    }
};
