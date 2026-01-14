<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Cambiar directamente a VARCHAR(500) para evitar problemas con TEXT
        DB::statement('ALTER TABLE empresas MODIFY api_key VARCHAR(500) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE empresas MODIFY api_key VARCHAR(255) NULL');
    }
};