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
        Schema::create('customer_odds', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('uid')->default('0')->comment('业务员用户ID');
            $table->integer('before_uid')->default('0')->comment('前业务员用户ID');
            $table->integer('creator_uid')->default('0')->comment('创建用户ID');
            $table->string('name', 255)->default('')->comment('商机名称');
            $table->string('eid', 255)->default('')->comment('客户名称');
            $table->string('source', 32)->default('')->comment('商机类型：1、线索；0、客户；');
            $table->string('types', 255)->default('')->comment('商机类型');
            $table->string('status', 255)->default('1')->comment('商机状态');
            $table->string('followed', 255)->default('1')->comment('是否关注');
            $table->text('description')->comment('商机描述');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('pid', 255)->default('""')->comment('客户名称');
            $table->engine = 'InnoDB';
            $table->comment('客户商机表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_odds');
    }
};
