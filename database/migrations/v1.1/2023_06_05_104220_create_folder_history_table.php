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
        Schema::create('folder_history', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('历史记录 id');
            $table->unsignedBigInteger('entid')->nullable()->default(0)->comment('企业 id');
            $table->char('uid', 32)->comment('修改用户');
            $table->unsignedBigInteger('folder_id')->comment('文件 id');
            $table->string('file_name')->comment('文件真实名称');
            $table->string('file_url')->comment('文件 url');
            $table->string('file_size', 32)->comment('文件大小');
            $table->unsignedInteger('version')->default(1)->comment('文件版本');
            $table->unsignedInteger('download_count')->nullable()->default(0)->comment('下载次数');
            $table->tinyInteger('upload_type')->comment('上传方式');
            $table->timestamp('created_at')->useCurrentOnUpdate()->useCurrent()->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('folder_history');
    }
};
