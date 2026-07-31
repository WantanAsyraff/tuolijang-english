<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contract_doc', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('uid')->default(0)->comment('经办人ID');
            $table->unsignedInteger('eid')->default(0)->comment('关联客户ID');
            $table->json('cid')->nullable()->comment('关联订单ID');
            $table->json('oid')->nullable()->comment('关联商机ID');
            $table->unsignedTinyInteger('link_type')->default(2)->comment('关联类型:2.订单 5.商机');
            $table->string('doc_name', 256)->comment('合同名称');
            $table->string('doc_no', 32)->comment('合同编号');
            $table->tinyInteger('status')->default(0)->comment('合同状态');
            $table->unsignedTinyInteger('sign_type')->default(1)->comment('签约方式:1.纸质签约 2.电子签约');
            $table->unsignedTinyInteger('term_type')->default(0)->comment('期限类型:0.无期限 1.固定期限 2.签约日起算');
            $table->unsignedInteger('date_count')->default(0)->comment('期限时长');
            $table->dateTime('start_date')->nullable()->comment('合同开始日期');
            $table->dateTime('end_date')->nullable()->comment('合同结束日期');
            $table->unsignedTinyInteger('sign_status')->default(0)->comment('签约状态');
            $table->dateTime('sign_date')->nullable()->comment('签约日期');
            $table->string('signature_sn', 32)->nullable()->comment('电子签订单号');
            $table->json('sign_file')->nullable()->comment('签署前文件');
            $table->string('file_id', 512)->nullable()->comment('临时文件ID');
            $table->string('app_url', 512)->nullable()->comment('签署小程序链接');
            $table->string('pc_url', 512)->nullable()->comment('签署pc链接');
            $table->string('sign_url', 512)->nullable()->comment('签署后文件地址');
            $table->string('sign_result', 512)->nullable()->comment('签署后本地文件地址');
            $table->unsignedInteger('approve_id')->default(0)->comment('关联审批ID');
            $table->unsignedInteger('is_verify')->default(1)->comment('是否需要审核');
            $table->timestamp('fail_time')->nullable()->comment('合同截止日期');
            $table->text('mark')->nullable()->comment('备注信息');
            $table->timestamps();
            $table->softDeletes();
            $table->comment('合同签约表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('contract_doc');
    }
};
