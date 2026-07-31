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
        Schema::create('rank_relation', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedBigInteger('level_id');
            $table->unsignedBigInteger('cate_id');
            $table->unsignedBigInteger('rank_id');
            $table->integer('number')->default(0)->comment('职级数');
            $table->tinyInteger('status')->default(0)->comment('状态:1=开启,0=关闭');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rank_relation');
    }
};
