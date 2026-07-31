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
        Schema::create('view_search', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('uid')->default('0')->comment('关联用户ID');
            $table->string('title', 255)->default('')->comment('视图名称');
            $table->text('content')->comment('视图内容');
            $table->string('category', 32)->default('')->comment('视图分类(参考枚举类目)');
            $table->integer('types')->default('0')->comment('视图类型：0-系统 1-个人');
            $table->integer('is_public')->default('0')->comment('是否公开：0-否 1-是');
            $table->integer('sort')->default('0')->comment('排序');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('视图搜索表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('view_search');
    }
};
