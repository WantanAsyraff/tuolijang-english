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
        Schema::create('client_file', function (Blueprint $table) {
            $table->increments('id')->comment('附件ID');
            $table->unsignedInteger('eid')->default(0)->comment('客户ID');
            $table->unsignedInteger('cid')->default(0)->comment('合同ID');
            $table->unsignedInteger('fid')->default(0)->comment('跟进记录ID');
            $table->unsignedInteger('vid')->default(0)->comment('发票申请ID');
            $table->unsignedInteger('uid')->default(0)->comment('上传用户ID');
            $table->string('name', 256)->default('')->comment('附件名称');
            $table->string('real_name', 256)->default('')->comment('附件原始名称');
            $table->char('att_dir', 200)->default('')->comment('附件路径');
            $table->char('thumb_dir', 200)->default('')->comment('附件压缩路径');
            $table->char('att_size', 32)->default('')->comment('附件大小');
            $table->char('att_type', 32)->default('')->comment('附件类型');
            $table->unsignedInteger('entid')->default(0)->comment('分后台ID');
            $table->unsignedTinyInteger('up_type')->default(1)->comment('上传方式：1、本地；2、七牛云；3、OSS；4、COS。');
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
        Schema::dropIfExists('client_file');
    }
};
