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
        Schema::table('attendance_group_member', function (Blueprint $table) {
            $table->integer('entid')->default(1)->comment('企业ID');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('attendance_group_member', function (Blueprint $table) {
            $table->dropColumn(['entid']);
        });
    }
};
