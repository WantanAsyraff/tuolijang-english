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
        Schema::create('work_media', function (Blueprint $table) {
            $table->comment('临时素材附件表');
            $table->bigIncrements('id')->comment('ID');
            $table->unsignedInteger('uid')->default(0)->index()->comment('业务员用户ID');
            $table->string('real_name')->default('')->comment('原始名称');
            $table->string('file_name')->default('')->comment('附件名称');
            $table->string('file_url')->default('')->comment('附件地址');
            $table->string('file_size')->default('')->comment('附件大小');
            $table->string('file_type')->default('')->comment('附件类型');
            $table->string('file_ext')->default('')->comment('扩展名');
            $table->string('file_md5')->default('')->comment('文件md5值');
            $table->unsignedTinyInteger('up_type')->default(1)->comment('上传方式：1、本地；2、七牛云；3、OSS；4、COS。');
            $table->unsignedInteger('link_id')->default(0)->index()->comment('关联数据ID');
            $table->string('link_type', 32)->default('')->index()->comment('关联数据类型');
            $table->string('media_id')->nullable()->index()->comment('临时素材ID');
            $table->timestamp('fail_time')->nullable()->comment('临时素材失效时间');
            $table->string('attach_id')->nullable()->index()->comment('临时附件ID');
            $table->timestamp('attach_fail')->nullable()->comment('临时附件过期时间');
            $table->string('job_id')->nullable()->index()->comment('分片上传素材任务ID');
            $table->string('media_type', 32)->default('')->comment('素材类型: image、voice、video、file');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('work_media');
    }
};
