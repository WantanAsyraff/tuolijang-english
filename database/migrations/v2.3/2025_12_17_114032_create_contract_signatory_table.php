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
        Schema::create('contract_signatory', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cid')->comment('合同id');
            $table->unsignedInteger('user_id')->default(0)->comment('用户id');
            $table->string('name', 256)->default('')->comment('经办人姓名');
            $table->string('company_name', 256)->default('')->comment('企业名称');
            $table->string('phone', 32)->default('')->comment('电话');
            $table->unsignedTinyInteger('types')->default(0)->comment('人员类型:0、发起人 1、签署人 2、签署企业');
            $table->string('e_userid', 64)->default('')->comment('电子签用户id');
            $table->string('e_openid', 64)->default('')->comment('电子签用户标识');
            $table->timestamp('sign_time')->nullable()->comment('签约时间');
            $table->unsignedTinyInteger('sign_status')->default(0)->comment('签约状态');
            $table->text('remark')->nullable()->comment('备注');
            $table->softDeletes();
            $table->timestamps();
            $table->index('cid');
            $table->comment('合同签署方');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('contract_signatory');
    }
};
