<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_crud_event', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->string('name', 255)->default('')->comment('事件名称');
            $table->string('event', 255)->default('')->comment('事件类型');
            $table->string('action', 500)->default('')->comment('触发动作');
            $table->integer('sort')->default(0)->comment('优先级');
            $table->integer('timer')->default(0)->comment('定时任务执行周期');
            $table->integer('target_crud_id')->default(0)->comment('目标实体');
            $table->integer('crud_approve_id')->default(0)->comment('实体内的审核ID');
            $table->tinyInteger('send_type')->default(0)->comment('发送用户类型:0=内部;1=外部');
            $table->text('send_user')->default('')->comment('发送用户');
            $table->string('notify_type', 255)->default('')->comment('通知类型');
            $table->longText('additional_search')->comment('附加搜索视图信息');
            $table->tinyInteger('additional_search_boolean')->default(0)->comment('附加搜索条件：0=符合其一 1= 符合全部');
            $table->longText('template')->comment('模板内容');
            $table->longText('field_options')->comment('字段信息');
            $table->longText('aggregate_target_search')->comment('聚合目标搜索');
            $table->tinyInteger('aggregate_target_search_boolean')->default(0)->comment('聚合目标搜索：0=符合其一 1= 符合全部');
            $table->longText('aggregate_data_search')->comment('聚合数据搜索');
            $table->tinyInteger('aggregate_data_search_boolean')->default(0)->comment('聚合数据搜索：0=符合其一 1= 符合全部');
            $table->longText('aggregate_data_field')->comment('分组字段关联');
            $table->longText('aggregate_field_rule')->comment('聚合字段规则');
            $table->longText('options')->comment('其他信息');
            $table->tinyInteger('status')->default(0)->comment('状态:0=关闭;1=开启');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_crud_event');
    }
};
