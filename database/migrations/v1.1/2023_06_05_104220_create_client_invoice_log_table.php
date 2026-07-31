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
        Schema::create('client_invoice_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entid')->comment('企业ID');
            $table->integer('invoice_id')->comment('发票ID');
            $table->integer('uid')->default(0)->comment('用户ID');
            $table->tinyInteger('type')->comment('操作类型');
            $table->text('operation')->nullable()->comment('日志内容');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('修改时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_invoice_log');
    }
};
