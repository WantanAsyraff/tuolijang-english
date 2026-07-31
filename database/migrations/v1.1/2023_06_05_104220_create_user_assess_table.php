<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_assess', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->string('name', 50)->default('')->comment('名称');
            $table->tinyInteger('period')->default(0)->comment('周期:1=周;2=月;3=年');
            $table->unsignedBigInteger('planid');
            $table->integer('frame_id')->default(0)->comment('组织架构ID');
            $table->integer('number')->default(0)->comment('考核批次ID');
            $table->integer('check_uid')->default(0)->comment('考核用户信息表ID');
            $table->integer('test_uid')->default(0)->comment('被考核用户信息表ID');
            $table->timestamp('start_time')->nullable()->comment('考核开始时间');
            $table->timestamp('make_time')->nullable()->comment('目标制定时间结束时间');
            $table->tinyInteger('make_status')->default(0)->comment('目标制定状态：0、未制定；1、已启用；2、草稿。');
            $table->timestamp('end_time')->nullable()->comment('考核结束时间');
            $table->unsignedTinyInteger('test_status')->default(0)->comment('自评状态：0、未评价；1、已评价；2、草稿；');
            $table->timestamp('check_end')->nullable()->comment('上级评价结束时间');
            $table->tinyInteger('check_status')->default(0)->comment('上级评价状态：0、未评价；1、已评价；2、草稿。');
            $table->timestamp('verify_time')->nullable()->comment('审核结束时间');
            $table->tinyInteger('verify_status')->default(0)->comment('审核状态：0、未审核；1、已审核；');
            $table->decimal('score', 5)->default(0)->comment('考核得分');
            $table->decimal('total', 5)->default(0)->comment('最高分');
            $table->integer('grade')->default(0)->comment('考核等级');
            $table->boolean('status')->default(false)->comment('考核状态：0、目标制定；1、自评期；2、上级评价；3、审核期；4、结束；');
            $table->unsignedTinyInteger('types')->default(0)->comment('评分方式：0、加权评分；1、加和评分');
            $table->unsignedTinyInteger('intact')->default(1)->comment('完整性：1、是；0、否');
            $table->unsignedTinyInteger('is_show')->default(0)->comment('是否启用：0、未启用；1、已启用；');
            $table->timestamp('delete')->nullable()->comment('删除时间');
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
        Schema::dropIfExists('user_assess');
    }
};
