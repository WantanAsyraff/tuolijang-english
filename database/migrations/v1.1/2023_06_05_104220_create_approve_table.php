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
        Schema::create('approve', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('card_id')->index('eb_enterprise_approve_card_id_foreign');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_approve_entid_foreign');
            $table->string('name', 32)->default('')->comment('审批名称');
            $table->string('icon', 256)->default('')->comment('审批图标');
            $table->string('color', 32)->default('')->comment('审批图标颜色');
            $table->text('info')->comment('审批说明');
            $table->text('config')->comment('表单配置详情');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：0、关闭；1、开启；');
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
        Schema::dropIfExists('approve');
    }
};
