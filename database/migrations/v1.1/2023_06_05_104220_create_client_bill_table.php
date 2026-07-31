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
        Schema::create('client_bill', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedInteger('eid')->default(0)->comment('客户ID');
            $table->integer('cid')->default(0)->comment('合同ID');
            $table->integer('cate_id')->default(0)->comment('续费类型ID');
            $table->integer('bill_cate_id')->default(0)->comment('续费类型ID');
            $table->unsignedTinyInteger('bill_types')->default(1)->comment('类型:0,支出;1,收入');
            $table->string('uid', 36)->default('')->comment('用户ID');
            $table->integer('invoice_id')->default(0)->comment('发票ID');
            $table->decimal('num', 10)->default(0)->comment('金额');
            $table->string('mark', 256)->nullable()->default('')->comment('备注');
            $table->tinyInteger('types')->default(0)->comment('类型：0，合同；1，续费；');
            $table->unsignedInteger('type_id')->default(0)->comment('支付方式ID');
            $table->string('pay_type', 256)->default('')->comment('支付方式名称');
            $table->timestamp('date')->nullable()->comment('收款日期');
            $table->timestamp('end_date')->nullable()->comment('续费结束日期');
            $table->string('bill_no', 30)->default('')->comment('付款单号');
            $table->tinyInteger('status')->default(0)->comment('类型：0，待审核；1，已通过；2，未通过');
            $table->string('fail_msg', 256)->default('')->comment('失败原因');
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
        Schema::dropIfExists('client_bill');
    }
};
