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
        Schema::create('bill_category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_bill_category_entid_foreign');
            $table->string('path')->default('')->comment('路径');
            $table->unsignedTinyInteger('level')->default(1)->comment('级别');
            $table->string('name', 120)->default('')->comment('分类名称');
            $table->string('cate_no', 30)->default('')->comment('分类编号');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('pid')->default(0)->comment('上级ID');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型:0,支出;1,收入');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_category');
    }
};
