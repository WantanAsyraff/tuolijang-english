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
        Schema::create('frame', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_frame_entid_index');
            $table->integer('pid')->default(0)->index('eb_enterprise_frame_pid_index')->comment('父级ID');
            $table->string('name', 100)->default('')->comment('部门名称');
            $table->string('path')->default('')->index('path')->comment('路径');
            $table->string('introduce')->default('')->comment('部门介绍');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('user_count')->default(0)->comment('用户数量');
            $table->integer('user_single_count')->default(0)->comment('单个部门总人数');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->integer('level')->default(0)->comment('等级');
            $table->timestamps();

            $table->index(['id', 'is_show'], 'show_cate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('frame');
    }
};
