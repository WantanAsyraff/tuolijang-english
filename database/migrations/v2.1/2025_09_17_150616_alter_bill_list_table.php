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
        Schema::table('bill_list', function (Blueprint $table) {
            $table->dropColumn(['order_id']);
            $table->string('file_id', 32)->default('')->comment('附件ID');
            $table->integer('user_id')->default('0')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('bill_list', function (Blueprint $table) {
            $table->dropColumn(['file_id']);
            $table->integer('user_id')->default('0')->comment('创建成员ID')->change();
            $table->integer('order_id')->default('0')->comment('订单ID');
        });
    }
};
