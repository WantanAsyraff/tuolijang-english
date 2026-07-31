<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->default(0)->comment('业务员ID');
            $table->integer('before_uid')->default(0)->comment('前业务员ID');
            $table->integer('creator_uid')->default(0)->comment('创建人ID');
            $table->string('customer_name', 50)->default('')->comment('客户名称');
            $table->string('customer_label', 255)->default('')->comment('客户标签');
            $table->string('customer_no', 15)->default('')->comment('客户编号');
            $table->integer('customer_way')->default(0)->comment('客户来源');
            $table->integer('un_followed_days')->default(0)->comment('未跟进天数');
            $table->decimal('amount_recorded', 10)->default(0)->comment('已入账金额');
            $table->decimal('amount_expend', 10)->default(0)->comment('已支出+金额');
            $table->decimal('invoiced_amount', 10)->default(0)->comment('已开票金额');
            $table->integer('contract_num')->default(0)->comment('合同数量');
            $table->integer('invoice_num')->default(0)->comment('发票数量');
            $table->integer('attachment_num')->default(0)->comment('附件数量');
            $table->integer('return_num')->default(0)->comment('退回次数');
            $table->tinyInteger('customer_followed')->default(0)->comment('客户关注');
            $table->tinyInteger('customer_status')->default(0)->comment('客户状态：0、未成交；1、已成交；1、已流失；');
            $table->string('area_cascade', 50)->default('')->comment('省市区');
            $table->string('b37a3f36', 255)->default('')->comment('备注');
            $table->string('b37a3f16', 255)->default('')->comment('企业电话');
            $table->string('9bfe77e4', 255)->default('')->comment('详细地址');
            $table->timestamp('last_follow_up_time')->nullable()->comment('最后跟进时间');
            $table->timestamp('collect_time')->nullable()->comment('领取时间');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
