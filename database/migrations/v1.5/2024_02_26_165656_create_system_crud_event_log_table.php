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
        Schema::create('system_crud_event_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->integer('event_id')->default(0)->index()->comment('触发器ID');
            $table->string('action', 100)->default('')->comment('触发类型');
            $table->string('result', 100)->default('')->comment('触发结果');
            $table->longText('parameter')->comment('出发参数');
            $table->longText('log')->comment('日志内容');

            $table->index(['crud_id', 'event_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_crud_form');
    }
};
