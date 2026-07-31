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
        Schema::create('storage', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('cid');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->string('creater', 36)->default('')->comment('创建用户ID');
            $table->string('name', 256)->default('')->comment('物资名称');
            $table->string('specs', 256)->default('')->comment('物资规格');
            $table->string('factory', 256)->default('')->comment('生产厂家');
            $table->string('units', 128)->default('')->comment('计量单位');
            $table->string('mark', 256)->default('')->comment('备注');
            $table->string('remark', 128)->default('')->comment('重要信息');
            $table->unsignedInteger('stock')->default(0)->comment('库存');
            $table->integer('used')->default(0)->comment('领用数量');
            $table->string('number', 32)->default('')->comment('物资编号');
            $table->unsignedTinyInteger('types')->default(0)->comment('物资类型：0、消耗物资；1、固定物资；');
            $table->unsignedTinyInteger('status')->default(0)->comment('物资状态：0、正常；1、已领用；3、维修中；4、已报废；	');
            $table->unsignedInteger('link_id')->default(0)->comment('关联记录ID');
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
        Schema::dropIfExists('storage');
    }
};
