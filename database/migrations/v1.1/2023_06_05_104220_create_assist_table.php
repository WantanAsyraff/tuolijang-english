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
        Schema::create('assist', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('辅助表自增id');
            $table->integer('main_id')->default(1)->comment('主表ID');
            $table->integer('aux_id')->default(1)->comment('副表ID');
            $table->string('type', 20)->default('1')->comment('类型,可用其他表名区分');
            $table->string('other', 2000)->default('')->comment('其他数据');
            $table->timestamp('created_at')->nullable()->comment('添加时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assist');
    }
};
