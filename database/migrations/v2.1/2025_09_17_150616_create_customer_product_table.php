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
        Schema::create('customer_product', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('uid')->default('0')->comment('用户ID');
            $table->string('name', 255)->nullable()->default('')->comment('产品名称');
            $table->integer('pid')->default('0')->comment('产品分类');
            $table->string('path', 255)->nullable()->default('')->comment('产品分类');
            $table->string('unit_name', 255)->nullable()->default('')->comment('单位名');
            $table->string('types', 255)->nullable()->default('1')->comment('产品类型');
            $table->string('number', 255)->nullable()->default('')->comment('产品编号');
            $table->text('description')->nullable()->comment('产品简介');
            $table->integer('spec_type')->default('0')->comment('产品规格：0、单规格；1、多规格；');
            $table->string('is_show', 255)->nullable()->default('1')->comment('产品状态');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('sort', 255)->comment('排序');
            $table->engine = 'InnoDB';
            $table->comment('产品表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_product');
    }
};
