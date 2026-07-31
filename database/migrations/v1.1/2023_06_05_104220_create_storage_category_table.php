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
        Schema::create('storage_category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('分类自增id');
            $table->integer('pid')->default(0)->index()->comment('父级ID');
            $table->string('cate_name', 100)->default('')->comment('分类名称');
            $table->string('path')->default('')->comment('路径');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('level')->default(0)->comment('等级');
            $table->unsignedTinyInteger('type')->default(0)->index()->comment('分类类型:0、消耗物资；1、固定物资；');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->timestamps();

            $table->index(['type', 'entid'], 'm_type');
            $table->index(['id', 'type', 'level'], 'type_cate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storage_category');
    }
};
