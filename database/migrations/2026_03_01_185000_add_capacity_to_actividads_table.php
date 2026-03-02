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
        Schema::table('actividads', function (Blueprint $table) {
            $table->integer('capacidad')->default(400)->after('costo');
            $table->integer('cupos_ocupados')->default(0)->after('capacidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividads', function (Blueprint $table) {
            $table->dropColumn(['capacidad', 'cupos_ocupados']);
        });
    }
};
