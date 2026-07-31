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
        Schema::create('contract', function (Blueprint $table) {
            $table->id();
            $table->integer('uid')->default(0)->comment('业务员ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('creator_uid')->default(0)->comment('创建人ID');
            $table->string('contract_name', 64)->default('')->comment('合同名称');
            $table->string('contract_no', 30)->default('')->comment('合同编号');
            $table->decimal('contract_price', 10)->default(0)->comment('合同金额');
            $table->decimal('received', 10)->default(0)->comment('回款金额');
            $table->decimal('surplus', 10)->default(0)->comment('尾款金额');
            $table->tinyInteger('contract_followed')->default(0)->comment('是否关注');
            $table->tinyInteger('contract_status')->default(0)->comment('合同状态：0、未开始；1、进行中；2、已结束；3、异常合同；');
            $table->tinyInteger('renew')->default(0)->comment('是否有续费：0、否；1、是；');
            $table->date('start_date')->nullable()->comment('合同开始时间');
            $table->date('end_date')->nullable()->comment('合同结束时间');
            $table->string('signing_status', 255)->default(0)->comment('签约状态');
            $table->string('b3733f36', 255)->default('')->comment('备注');
            $table->string('contract_category', 255)->default('[]')->comment('合同分类');
            $table->tinyInteger('is_abnormal')->default(0)->comment('是否异常：1、是；0、否；');
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
        Schema::dropIfExists('contract');
    }
};
