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
        // Tabla de períodos de recuperación
        Schema::create('recovery_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('school_period_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            $table->decimal('min_failing_grade', 5, 2)->default(0);
            $table->decimal('max_failing_grade', 5, 2)->default(9.99);
            $table->decimal('min_recovery_grade', 5, 2)->default(10.00);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('school_period_id')->references('id')->on('school_periods')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['empresa_id', 'sucursal_id']);
            $table->index('school_period_id');
            $table->index('is_active');
            $table->index('start_date');
            $table->index('end_date');
        });

        // Tabla de registros académicos (historial completo)
        Schema::create('academic_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('matricula_id');
            $table->unsignedBigInteger('school_period_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('educational_level_id');
            $table->string('grade');
            $table->string('section');
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('status')->default('enrolled');
            $table->boolean('approved')->default(false);
            $table->unsignedBigInteger('recovery_period_id')->nullable();
            $table->decimal('recovery_grade', 5, 2)->nullable();
            $table->string('recovery_status')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('promoted')->default(false);
            $table->boolean('repeated')->default(false);
            $table->boolean('withdrawn')->default(false);
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedBigInteger('recorded_by');
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
            
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('matricula_id')->references('id')->on('matriculas')->onDelete('cascade');
            $table->foreign('school_period_id')->references('id')->on('school_periods')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('program_id')->references('id')->on('programas')->onDelete('cascade');
            $table->foreign('educational_level_id')->references('id')->on('educational_levels')->onDelete('cascade');
            $table->foreign('recovery_period_id')->references('id')->on('recovery_periods')->onDelete('set null');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->foreign('recorded_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->index(['empresa_id', 'sucursal_id']);
            $table->index('student_id');
            $table->index('matricula_id');
            $table->index('school_period_id');
            $table->index('subject_id');
            $table->index('program_id');
            $table->index('status');
            $table->index('approved');
            $table->index('promoted');
            $table->index(['grade', 'section']);
            
            // Índice único para evitar duplicados
            $table->unique(['student_id', 'school_period_id', 'subject_id'], 'unique_student_period_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_records');
        Schema::dropIfExists('recovery_periods');
    }
};