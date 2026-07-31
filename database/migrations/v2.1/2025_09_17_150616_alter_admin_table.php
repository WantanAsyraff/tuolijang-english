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
        Schema::table('admin', function (Blueprint $table) {
            $table->integer('work_member_id')->default('0')->comment('企业微信成员id');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('admin', function (Blueprint $table) {
            $table->dropColumn(['work_member_id']);
        });
    }
};
