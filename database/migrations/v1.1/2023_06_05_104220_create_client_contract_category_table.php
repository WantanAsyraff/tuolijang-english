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
        Schema::create('client_contract_category', function (Blueprint $table) {
            $table->comment('合同分类');
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('pid')->default(0)->comment('上级ID');
            $table->string('path')->default('')->comment('路径');
            $table->tinyInteger('level')->default(1)->comment('级别');
            $table->unsignedBigInteger('entid');
            $table->integer('bill_cate_id')->default(0)->comment('账目分类ID');
            $table->string('bill_cate_path')->default('')->comment('账目分类路径');
            $table->string('name', 50)->default('')->comment('分类名称');
            $table->string('cate_no', 30)->default('')->comment('分类编号');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('client_contract_category');
    }
};
