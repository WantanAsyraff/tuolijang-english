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
        Schema::create('client_invoice', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->string('uid', 36)->default('')->comment('业务员ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('cid')->default(0)->comment('合同ID');
            $table->integer('category_id')->default(0)->comment('发票类目ID');
            $table->string('name', 128)->default('0')->comment('发票名称');
            $table->string('num', 32)->nullable()->comment('发票编号');
            $table->decimal('price', 10)->default(0)->comment('合同金额');
            $table->decimal('amount', 10)->default(0)->comment('发票金额');
            $table->string('types', 32)->default('')->comment('发票类型');
            $table->string('title', 128)->default('')->comment('发票抬头');
            $table->string('ident', 32)->default('')->comment('纳税人识别号');
            $table->string('bank', 32)->default('')->comment('开户行');
            $table->string('account', 128)->default('')->comment('开户账号');
            $table->string('address', 256)->default('')->comment('开票地址');
            $table->string('tel', 32)->default('')->comment('电话');
            $table->string('collect_name', 32)->default('')->comment('邮寄联系人');
            $table->string('collect_tel', 32)->default('')->comment('邮寄联系电话');
            $table->string('collect_type', 32)->default('')->comment('邮寄方式');
            $table->string('collect_email', 32)->default('')->comment('邮寄邮箱');
            $table->string('mail_address', 32)->default('')->comment('邮寄地址');
            $table->string('invoice_type', 32)->nullable()->default('')->comment('开票方式');
            $table->string('invoice_address', 256)->nullable()->default('')->comment('开票地址');
            $table->tinyInteger('status')->default(0)->comment('发票状态 -1：开票撤回；0：待开票；1：已开票；2:已拒绝；3：申请作废；4:同意作废；5：拒绝作废；6：作废撤回；');
            $table->tinyInteger('invalid')->default(0)->comment('作废状态: 0，默认；-1，撤回；1，待审核；2，审核通过；3，审核未通过');
            $table->date('bill_date')->nullable()->comment('开票日期');
            $table->date('real_date')->nullable()->comment('实际开票日期');
            $table->text('mark')->comment('备注内容');
            $table->string('remark', 256)->default('')->comment('开票备注');
            $table->string('card_remark', 256)->default('')->comment('业务员备注');
            $table->string('finance_remark', 256)->default('')->comment('财务备注');
            $table->string('creator', 36)->default('')->comment('创建人ID');
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
        Schema::dropIfExists('client_invoice');
    }
};
