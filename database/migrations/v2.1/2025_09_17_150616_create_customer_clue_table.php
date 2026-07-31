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
        Schema::create('customer_clue', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('uid')->default('0')->comment('业务员用户ID');
            $table->integer('before_uid')->default('0')->comment('前业务员用户ID');
            $table->integer('creator_uid')->default('0')->comment('创建用户ID');
            $table->string('name', 255)->nullable()->default('')->comment('线索名称');
            $table->string('source', 255)->nullable()->default('')->comment('线索来源');
            $table->string('pool', 255)->nullable()->default('')->comment('线索池');
            $table->string('phone', 255)->nullable()->default('')->comment('联系电话');
            $table->string('status', 255)->nullable()->default('1')->comment('线索状态');
            $table->string('followed', 255)->nullable()->default('1')->comment('是否关注');
            $table->integer('return_num')->default('0')->comment('退回次数');
            $table->text('mark')->nullable()->comment('备注');
            $table->string('userid', 255)->nullable();
            $table->string('external_userid', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('area_cascade', 255)->nullable()->default('')->comment('省市区');
            $table->string('address', 255)->nullable()->default('')->comment('详细地址');
            $table->string('customer_label', 255)->nullable()->default('')->comment('客户标签');
            $table->timestamp('createtime')->nullable()->comment('线索时间');
            $table->engine = 'InnoDB';
            $table->comment('客户线索表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_clue');
    }
};
