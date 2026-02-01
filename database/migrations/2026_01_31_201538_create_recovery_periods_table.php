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
        Schema::create('recovery_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('school_period_id');
            
            // Información del período de recuperación
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_deadline');
            
            // Configuración de recuperación
            $table->decimal('minimum_grade', 5, 2)->default(6.0); // Nota mínima para recuperar
            $table->decimal('maximum_grade', 5, 2)->default(7.0); // Nota máxima que se puede obtener
            $table->integer('max_recoveries_per_student')->default(3); // Máximo de materias por estudiante
            $table->boolean('allows_previous_periods')->default(false); // Permite recuperar de períodos anteriores
            
            // Estado del período
            $table->string('status', 50)->default('draft'); // draft, active, closed
            $table->boolean('is_active')->default(true);
            
            // Costos
            $table->decimal('cost_per_recovery', 10, 2)->default(0.00);
            $table->decimal('cost_per_subject', 10, 2)->default(0.00);
            
            // Requisitos
            $table->text('requirements')->nullable();
            $table->text('documentation_required')->nullable();
            
            // Información adicional
            $table->integer('max_students')->nullable(); // Máximo de estudiantes permitidos
            $table->integer('current_students')->default(0); // Estudiantes actualmente inscritos
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('school_period_id')->references('id')->on('school_periods')->onDelete('cascade');
            
            $table->index(['empresa_id', 'sucursal_id']);
            $table->index('school_period_id');
            $table->index('status');
            $table->index('is_active');
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recovery_periods');
    }
};