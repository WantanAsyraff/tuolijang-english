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
        Schema::create('hay_group_data', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedInteger('group_id')->comment('评估表ID');
            $table->integer('uid')->comment('业务员ID');
            $table->integer('col1')->comment('职位');
            $table->integer('col2')->comment('专业知识水平');
            $table->integer('col3')->comment('管理诀窍');
            $table->integer('col4')->comment('人际关系技巧');
            $table->integer('col5')->comment('评分');
            $table->integer('col6')->comment('思维环境');
            $table->integer('col7')->comment('思维难度');
            $table->integer('col8')->comment('评分');
            $table->integer('col9')->comment('行动自由度');
            $table->integer('col10')->comment('职务责任');
            $table->string('col11', 10)->comment('职务影响结果');
            $table->integer('col12')->comment('评分');
            $table->string('col13', 10)->comment('α');
            $table->string('col14', 10)->comment('β');
            $table->string('col15', 10)->comment('岗位分数');
            $table->string('col16', 10)->comment('岗位系数');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('hay_group_data');
    }
};
