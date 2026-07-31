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
        Schema::create('client_contract', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('uid', 36)->default('')->comment('业务员ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('category_id')->default(0)->comment('合同分类ID');
            $table->string('title')->default('')->comment('合同名称');
            $table->string('contract_no', 60)->default('')->comment('合同编号');
            $table->decimal('price', 10)->default(0)->comment('合同金额');
            $table->decimal('received', 10)->default(0)->comment('回款金额');
            $table->decimal('surplus', 10)->default(0)->comment('尾款金额');
            $table->date('start_date')->nullable()->comment('合同开始时间');
            $table->date('end_date')->nullable()->comment('合同结束时间');
            $table->text('mark')->comment('备注内容');
            $table->unsignedTinyInteger('renew')->default(0)->comment('是否有续费');
            $table->unsignedTinyInteger('follow')->default(0)->comment('是否关注');
            $table->unsignedTinyInteger('up_follow')->default(0)->comment('上级是否关注');
            $table->string('creator', 36)->default('')->comment('创建人ID');
            $table->unsignedTinyInteger('is_abnormal')->default(0)->comment('是否异常：1、是；0、否；');
            $table->tinyInteger('sign_status')->default(0)->comment('签约状态：0：未签约；1：已签约；2：作废；');
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
        Schema::dropIfExists('client_contract');
    }
};
