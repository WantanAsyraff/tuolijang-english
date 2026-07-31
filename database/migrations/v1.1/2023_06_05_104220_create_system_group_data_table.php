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
        Schema::create('system_group_data', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('组合数据自增id');
            $table->integer('group_id')->default(0)->index()->comment('组合数据数组ID(关联system_group表id)');
            $table->text('value')->comment('数据组对应的数据值（json数据）');
            $table->integer('sort')->default(0)->comment('数据排序');
            $table->tinyInteger('status')->default(0)->comment('状态 1=开启,0=关闭');
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
        Schema::dropIfExists('system_group_data');
    }
};
