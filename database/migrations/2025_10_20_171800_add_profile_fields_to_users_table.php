<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('sucursal_id');
            $table->json('preferred_devices')->nullable()->after('avatar');
            $table->json('common_locations')->nullable()->after('preferred_devices');
            $table->integer('total_session_time')->default(0)->after('common_locations'); // en minutos
            $table->json('security_alerts')->nullable()->after('total_session_time');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'preferred_devices',
                'common_locations',
                'total_session_time',
                'security_alerts'
            ]);
        });
    }
};
