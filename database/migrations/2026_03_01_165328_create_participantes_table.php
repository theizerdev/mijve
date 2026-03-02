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
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('actividad_id')->constrained('actividads')->onDelete('cascade');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cedula')->nullable();
            $table->string('telefono_principal')->nullable();
            $table->string('telefono_alternativo')->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento');
            $table->integer('edad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};
