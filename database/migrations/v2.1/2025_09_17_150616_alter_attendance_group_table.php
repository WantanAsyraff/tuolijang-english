<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::table('attendance_group', function (Blueprint $table) {
            $table->integer('is_map')->default(1)->comment('地图位置打卡');
            $table->integer('is_wifi')->default(0)->comment('Wi-Fi打卡');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('attendance_group', function (Blueprint $table) {
            $table->dropColumn(['is_map', 'is_wifi']);
        });
    }
};
