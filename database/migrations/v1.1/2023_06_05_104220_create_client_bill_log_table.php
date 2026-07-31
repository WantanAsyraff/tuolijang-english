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
        Schema::create('client_bill_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->integer('entid')->default(0)->index('eb_enterprise_client_bill_list_log_entid_index')->comment('企业ID');
            $table->integer('bill_list_id')->default(0)->comment('付款流水ID');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->tinyInteger('type')->default(0)->comment('操作类型');
            $table->text('operation')->comment('日志内容');
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
        Schema::dropIfExists('client_bill_log');
    }
};
