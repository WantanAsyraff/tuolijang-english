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
        Schema::create('system_attach', function (Blueprint $table) {
            $table->increments('id')->comment('附件ID');
            $table->unsignedInteger('entid')->default(0)->index('entid')->comment('分后台ID');
            $table->string('uid', 36)->default('')->index('uid')->comment('上传用户uid');
            $table->char('name', 100)->default('')->comment('附件名称');
            $table->char('real_name', 100)->default('')->comment('附件原始名称');
            $table->char('att_dir', 200)->default('')->comment('附件路径');
            $table->char('thumb_dir', 200)->default('')->comment('附件压缩路径');
            $table->char('att_size', 32)->default('')->comment('附件大小');
            $table->char('att_type', 32)->default('')->comment('附件类型');
            $table->char('file_ext', 32)->default('')->comment('文件扩展名');
            $table->unsignedInteger('cid')->default(0)->comment('分类ID');
            $table->unsignedTinyInteger('up_type')->default(1)->comment('上传方式：1、本地；2、七牛云；3、OSS；4、COS。');
            $table->unsignedTinyInteger('way')->default(0)->comment('来源：1、总后台；2、分后台；3、用户。');
            $table->tinyInteger('relation_type')->default(0)->comment('模块:1、汇报；');
            $table->integer('relation_id')->default(0)->comment('模块ID');
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
        Schema::dropIfExists('system_attach');
    }
};
