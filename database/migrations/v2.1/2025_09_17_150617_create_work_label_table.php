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
        Schema::create('work_label', function (Blueprint $table) {
            $table->id();
            $table->string('corp_id', 18)->default('')->comment('企业微信id');
            $table->integer('group_id')->default('0')->comment('标签组id');
            $table->string('group_name', 50)->default('')->comment('标签组名称');
            $table->string('name', 50)->default('')->comment('标签名称');
            $table->integer('sort')->default('0')->comment('排序');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信标签');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_label');
    }
};
