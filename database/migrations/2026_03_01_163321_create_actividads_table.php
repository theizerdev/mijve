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
        Schema::create('actividads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->text('descripcion')->nullable();
            $table->string('status')->default('Pendiente'); // Activo, Inactivo, Pendiente
            $table->integer('edad_desde')->unsigned();
            $table->integer('edad_hasta')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividads');
    }
};
