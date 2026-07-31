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
        Schema::create('enterprise_user_work', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('card_id')->default(0)->index('eb_enterprise_user_work_card_id_foreign')->comment('企业用户信息(enterprise_user_card)ID');
            $table->date('start_time')->nullable()->comment('开始时间');
            $table->date('end_time')->nullable()->comment('结束时间');
            $table->string('company', 200)->default('')->comment('所在公司');
            $table->string('position', 50)->default('')->comment('职位');
            $table->string('describe', 500)->default('')->comment('工作描述');
            $table->string('quit_reason', 500)->default('')->comment('离职原因');
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
        Schema::dropIfExists('enterprise_user_work');
    }
};
