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
        Schema::create('promotion', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->string('name', 120)->default('')->comment('晋升名称');
            $table->integer('sort')->default(0)->comment('排序');
            $table->unsignedTinyInteger('status')->default(1)->comment('1、展示; 0、关闭');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_cate');
    }
};
