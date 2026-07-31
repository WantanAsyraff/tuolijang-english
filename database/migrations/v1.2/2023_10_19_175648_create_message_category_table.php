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
        Schema::create('message_category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('pid')->default(0)->index()->comment('父级ID');
            $table->string('cate_name', 100)->default('')->comment('分类名称');
            $table->string('path')->default('')->comment('路径');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('pic', 255)->default('')->comment('图标');
            $table->tinyInteger('is_show')->default(1)->comment('是否显示');
            $table->tinyInteger('uni_show')->default(1)->comment('移动端是否显示');
            $table->integer('level')->default(0)->comment('等级');
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
        Schema::dropIfExists('message_category');
    }
};
