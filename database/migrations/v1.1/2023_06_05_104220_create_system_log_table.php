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
        Schema::create('system_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('日志自增id');
            $table->integer('admin_id')->default(0)->index()->comment('管理员ID');
            $table->string('admin_name', 64)->default('')->index()->comment('管理员姓名');
            $table->string('path', 128)->default('')->comment('链接');
            $table->string('method', 20)->default('')->comment('访问方式');
            $table->string('event_name', 60)->default('')->comment('行为');
            $table->string('type', 32)->default('')->index()->comment('类型');
            $table->string('terminal', 100)->default('')->index()->comment('访问终端');
            $table->string('last_ip', 45)->comment('访问ip');
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
        Schema::dropIfExists('system_log');
    }
};
