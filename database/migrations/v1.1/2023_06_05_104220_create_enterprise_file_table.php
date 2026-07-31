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
        Schema::create('enterprise_file', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('name')->default('')->comment('文件名称');
            $table->string('image')->default('')->comment('文件封面');
            $table->string('real_name')->default('')->comment('文件原始名称');
            $table->string('path')->default('')->comment('文件夹路径');
            $table->string('url')->default('')->comment('访问完整路径');
            $table->string('file_id', 32)->default('')->comment('文件标识');
            $table->string('size', 32)->default('0')->comment('文件大小(单位KB)');
            $table->string('type', 100)->default('')->comment('文件类型');
            $table->string('uid', 36)->default('')->comment('拥有人UID');
            $table->string('edit_uid', 36)->default('')->comment('修改人UID');
            $table->integer('version')->default(0)->comment('文件版本号');
            $table->string('other', 2000)->default('')->comment('其他参数');
            $table->integer('cate_id')->default(0)->comment('分类ID');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('download_count')->default(0)->comment('下载次数');
            $table->tinyInteger('upload_type')->default(0)->comment('上传文件驱动类型1=本地,2=七牛,3=oss,4=cos');
            $table->tinyInteger('is_master')->default(0)->comment('是否是主文件');
            $table->boolean('is_template')->default(false);
            $table->boolean('status');
            $table->timestamp('delete')->nullable()->comment('是否删除');
            $table->timestamps();

            $table->unique(['file_id', 'version'], 'version');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_file');
    }
};
