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
        Schema::table('customer', function (Blueprint $table) {
            $table->string('userid', 256)->default('')->comment('企微用户ID');
            $table->string('external_userid', 256)->default('')->comment('企微客户ID');
            $table->string('clue_id', 255)->nullable()->comment('关联线索');
            $table->json('member')->nullable()->comment('协作者');
            $table->string('customer_name', 255)->nullable()->comment('客户名称')->change();
            $table->string('customer_label', 255)->nullable()->comment('客户标签')->change();
            $table->string('customer_way', 255)->nullable()->comment('客户来源')->change();
            $table->string('customer_followed', 255)->nullable()->default('1')->comment('是否关注')->change();
            $table->string('customer_status', 255)->nullable()->default('0')->comment('客户状态')->change();
            $table->string('area_cascade', 255)->nullable()->default('')->comment('省市区')->change();
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn(['userid', 'external_userid', 'c3c44e85', 'cf4bb8ff', 'c5d01f85', 'c093469d', 'clue_id', 'c55a92f6', 'member']);
            $table->string('customer_name')->length(255)->default('')->comment('客户名称')->change();
            $table->string('customer_label')->length(255)->default('""')->comment('客户标签')->change();
            $table->string('customer_way')->length(255)->default('"[\"10\"]"')->comment('客户来源')->change();
            $table->string('customer_followed')->length(255)->default('')->comment('是否关注')->change();
            $table->string('customer_status')->length(255)->default('"[\"2\"]"')->comment('客户状态')->change();
            $table->string('area_cascade')->length(255)->default('"[\"33\",\"3966\",\"3970\"]"')->comment('省市区')->change();
            $table->string('b37a3f16')->length(255)->default('')->comment('企业电话')->change();
            $table->string('9bfe77e4')->length(255)->default('')->comment('详细地址')->change();
            $table->string('c839a357')->length(255)->default('')->comment('备注')->change();
            $table->string('c254fbdb')->length(255)->default('')->comment('附件');
        });
    }
};
