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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->date('fecha_nacimiento');
            $table->string('codigo', 8)->unique();
            $table->string('documento_identidad')->unique();
            $table->string('grado');
            $table->string('seccion');
            $table->foreignId('nivel_educativo_id')->constrained('niveles_educativos');
            $table->foreignId('turno_id')->constrained('turnos');
            $table->foreignId('school_periods_id')->constrained('school_periods');
            $table->string('foto')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};