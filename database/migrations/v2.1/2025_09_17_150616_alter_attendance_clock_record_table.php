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
        Schema::table('attendance_clock_record', function (Blueprint $table) {
            $table->integer('clock_type')->default(0)->comment('打卡方式：0、位置；1、Wifi');
            $table->string('mac', 32)->default('')->comment('Wi-Fi打卡Mac地址');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('attendance_clock_record', function (Blueprint $table) {
            $table->dropColumn(['clock_type', 'mac']);
        });
    }
};
