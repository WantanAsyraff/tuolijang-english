<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('export_record', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('uid')->comment('用户ID');
            $table->string('name')->comment('文件名');
            $table->unsignedInteger('success_count')->default(0)->comment('成功数量');
            $table->unsignedInteger('fail_count')->default(0)->comment('失败数量');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态:0、待处理,1、成功,2、失败');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型:0、导出;1、导入;');
            $table->string('file_path', 255)->comment('文件路径');
            $table->unsignedTinyInteger('file_status')->default(1)->comment('文件状态:0、正常,1、删除');
            $table->string('fail_msg', 512)->nullable()->comment('失败原因');
            $table->string('module', 32)->nullable()->comment('关联业务模块');
            $table->timestamps();
            $table->comment('导入导出记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('export_record');
    }
};
