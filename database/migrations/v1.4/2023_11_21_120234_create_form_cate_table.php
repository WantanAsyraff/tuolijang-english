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
        Schema::create('form_cate', function (Blueprint $table) {
            $table->id();
            $table->string('title',64)->default('')->comment('分组名称');
            $table->unsignedInteger('sort')->default(0)->comment('分组排序');
            $table->unsignedTinyInteger('types')->default(0)->comment('分组类型：1、客户；2、合同；3、联系人；');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1、显示；0、隐藏；');
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
        Schema::dropIfExists('form_cate');
    }
};
