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
        Schema::create('system_crud_operate', function (Blueprint $table) {
            $table->id();
            $table->integer('crud_id')->default('0')->comment('crud的主键id');
            $table->string('name', 255)->default('')->comment('操作名称');
            $table->string('operate', 255)->default('')->comment('操作唯一值');
            $table->integer('sort')->default('0')->comment('排序');
            $table->integer('system_crud_form_id')->default('0')->comment('选择的表单ID');
            $table->integer('operate_type')->default('0')->comment('0=列表头部，1=列表中');
            $table->integer('status')->default('0')->comment('状态');
            $table->integer('action_type')->default('0')->comment('0=新增，1=编辑');
            $table->string('popup_name', 255)->default('')->comment('弹窗标题');
            $table->text('use_rule')->comment('启用规则');
            $table->text('options')->comment('参数设置');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('system_crud_operate');
    }
};
