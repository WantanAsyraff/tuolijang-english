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
        Schema::create('storage_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('storage_id')->index('eb_storage_record_storage_id_foreign');
            $table->unsignedTinyInteger('storage_type')->nullable()->default(0)->comment('物资类型');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->string('creater', 36)->nullable()->default('')->comment('创建用户ID');
            $table->unsignedInteger('card_id')->default(0)->comment('关联用户ID');
            $table->unsignedInteger('frame_id')->default(0)->comment('关联组织架构ID');
            $table->string('info', 256)->default('')->comment('操作说明');
            $table->string('mark', 256)->default('')->comment('备注信息');
            $table->decimal('price')->default(0)->comment('单价');
            $table->unsignedDecimal('total')->default(0)->comment('总价');
            $table->unsignedInteger('num')->default(0)->comment('物资数量');
            $table->unsignedTinyInteger('types')->default(0)->comment('操作类型：0、入库；1、领用；2、归还；3、维修；4、报废；5、维修完成；');
            $table->unsignedTinyInteger('status')->default(0)->comment('当前物资状态');
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
        Schema::dropIfExists('storage_record');
    }
};
