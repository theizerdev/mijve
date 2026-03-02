<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->string('tipo_miembro')->nullable()->after('estado_civil');
        });
    }

    public function down(): void
    {
        Schema::table('participantes', function (Blueprint $table) {
            $table->dropColumn('tipo_miembro');
        });
    }
};
