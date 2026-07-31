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
        Schema::create('customer_product_category', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('uid')->default('0')->comment('用户ID');
            $table->integer('pid')->default('0')->comment('父id');
            $table->string('path', 255)->default('')->comment('路径');
            $table->string('name', 255)->default('')->comment('分类名称');
            $table->integer('level')->default('0')->comment('等级');
            $table->integer('status')->default('1')->comment('状态');
            $table->integer('sort')->default('0')->comment('排序');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('产品分类表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_product_category');
    }
};
