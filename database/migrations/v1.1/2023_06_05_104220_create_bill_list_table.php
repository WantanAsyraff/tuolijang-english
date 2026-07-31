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
        Schema::create('bill_list', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_bill_list_entid_foreign');
            $table->char('uid', 36)->index('eb_enterprise_bill_list_user_id_index')->comment('创建成员ID');
            $table->integer('cate_id')->default(0)->comment('财务流水分类ID');
            $table->unsignedDecimal('num', 12)->default(0)->comment('变动金额');
            $table->timestamp('edit_time')->nullable()->comment('变动时间');
            $table->tinyInteger('types')->default(0)->comment('变动类型:1=收入,0=支出');
            $table->unsignedInteger('type_id')->default(0)->comment('支付方式ID');
            $table->string('pay_type', 256)->default('')->comment('支付方式名称');
            $table->string('mark')->default('')->comment('备注信息');
            $table->unsignedInteger('link_id')->default(0)->comment('关联ID');
            $table->string('link_cate', 32)->default('')->comment('关联类型');
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
        Schema::dropIfExists('bill_list');
    }
};
