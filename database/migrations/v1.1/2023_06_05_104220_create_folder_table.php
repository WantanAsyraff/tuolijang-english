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
        Schema::create('folder', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('文件 id');
            $table->unsignedTinyInteger('type')->default(0)->comment('0:文件 1:目录');
            $table->string('name')->comment('文件名称');
            $table->string('path', 500)->comment('文件路径');
            $table->unsignedBigInteger('pid')->default(0)->comment('父级文件 id');
            $table->char('uid', 32)->comment('用户 id');
            $table->string('file_name')->nullable()->comment('文件真实名称');
            $table->string('file_ext', 16)->nullable()->default('')->comment('文件后缀');
            $table->string('file_url')->nullable()->comment('文件 url');
            $table->string('file_sn')->nullable()->comment('文件编号');
            $table->string('file_size', 32)->nullable()->comment('文件大小');
            $table->string('file_type', 32)->nullable()->comment('文件类型');
            $table->string('upload_type')->nullable();
            $table->unsignedBigInteger('entid')->nullable()->default(0)->comment('企业 id');
            $table->unsignedInteger('download_count')->nullable()->default(0)->comment('下载次数');
            $table->unsignedInteger('version')->nullable()->default(1)->comment('文件版本');
            $table->unsignedTinyInteger('is_temp')->nullable()->default(0)->comment('临时文件');
            $table->unsignedTinyInteger('is_share')->nullable()->default(0)->comment('是否共享');
            $table->unsignedTinyInteger('is_collect')->nullable()->default(0)->comment('是否收藏');
            $table->unsignedTinyInteger('is_shortcut')->nullable()->default(0)->comment('是否常用');
            $table->unsignedTinyInteger('is_del')->nullable()->default(0)->comment('是否删除');
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
        Schema::dropIfExists('folder');
    }
};
