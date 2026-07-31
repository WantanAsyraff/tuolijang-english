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
        Schema::create('task', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业id，0=全局任务');
            $table->string('name', 32)->default('')->comment('任务名称');
            $table->enum('period', ['year', 'month', 'week', 'day', 'second', 'once'])->default('second')->comment('任务执行类型');
            $table->tinyInteger('persist')->default(0)->comment('是否永久执行');
            $table->integer('run_count')->default(1)->comment('执行次数最少1次');
            $table->integer('exe_count')->default(0)->comment('已经执行次数');
            $table->string('class_name')->default('')->comment('执行任务类名');
            $table->string('action')->default('')->comment('执行任务方法名');
            $table->string('interval', 2000)->default('')->comment('执行时间,一般为json格式');
            $table->dateTime('end_time')->nullable()->comment('结束时间');
            $table->unsignedInteger('rate')->default(0)->comment('间隔时间：n天、n月、n年、n周');
            $table->string('parameter', 2000)->default('')->comment('执行参数一般为json格式');
            $table->string('uniqued', 32)->default('')->comment('任务唯一值');
            $table->timestamp('delete')->nullable()->comment('是否删除');
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
        Schema::dropIfExists('task');
    }
};
