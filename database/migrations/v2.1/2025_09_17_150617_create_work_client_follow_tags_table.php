<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::create('work_client_follow_tags', function (Blueprint $table) {
            $table->id();
            $table->integer('follow_id')->default('0')->comment('跟踪人id');
            $table->string('group_name', 255)->default('')->comment('该成员添加此外部联系人所打标签的分组名称');
            $table->string('tag_name', 255)->default('')->comment('该成员添加此外部联系人所打标签名称');
            $table->integer('type')->default('0')->comment('1-企业设置，2-用户自定义，3-规则组标签');
            $table->string('tag_id', 32)->default('')->comment('该成员添加此外部联系人所打企业标签的id，用户自定义类型标签（type=2）不返回');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信客户跟踪标签');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_client_follow_tags');
    }
};
