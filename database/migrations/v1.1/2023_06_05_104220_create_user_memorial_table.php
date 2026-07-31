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
        Schema::create('user_memorial', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 36)->default('')->comment('用户ID');
            $table->string('title')->default('')->comment('标题名称');
            $table->mediumText('content')->comment('内容');
            $table->integer('pid')->default(0)->comment('分类ID');
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
        Schema::dropIfExists('user_memorial');
    }
};
