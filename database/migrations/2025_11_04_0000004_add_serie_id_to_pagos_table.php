<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('serie_id')->nullable()->after('id');
            $table->foreign('serie_id')->references('id')->on('series')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['serie_id']);
            $table->dropColumn('serie_id');
        });
    }
};
