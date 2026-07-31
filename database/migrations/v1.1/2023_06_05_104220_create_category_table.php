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
        Schema::create('category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('分类自增id');
            $table->integer('pid')->default(0)->index()->comment('父级ID');
            $table->string('cate_name', 100)->default('')->comment('分类名称');
            $table->string('path')->default('')->comment('路径');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('pic', 128)->default('')->comment('图标');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->integer('level')->default(0)->comment('等级');
            $table->string('type', 32)->default('')->index()->comment('分类类型');
            $table->string('keyword', 32)->default('')->comment('标记词');
            $table->integer('entid')->default(0)->comment('平台编号；0、总后台；');
            $table->timestamps();

            $table->index(['type', 'entid'], 'm_type');
            $table->index(['id', 'is_show'], 'show_cate');
            $table->index(['id', 'type', 'level', 'is_show'], 'type_cate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category');
    }
};
