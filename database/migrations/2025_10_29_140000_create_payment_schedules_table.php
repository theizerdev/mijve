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
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->integer('numero_cuota');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('estado')->default('pendiente'); // pendiente, pagado, vencido
            $table->timestamps();
            
            $table->index(['matricula_id', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_schedules');
    }
};