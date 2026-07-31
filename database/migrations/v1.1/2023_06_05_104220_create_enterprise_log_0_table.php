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
        Schema::create('enterprise_log_0', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('id');
            $table->string('uid', 36)->default('')->index()->comment('用户ID');
            $table->string('user_name', 64)->default('')->index()->comment('管理员姓名');
            $table->string('path', 128)->default('')->comment('链接');
            $table->string('method', 20)->default('')->comment('访问方式');
            $table->string('event_name', 60)->default('')->comment('行为');
            $table->integer('entid')->default(0)->index()->comment('企业ID');
            $table->string('type', 32)->default('')->index()->comment('类型');
            $table->string('terminal', 100)->default('')->index()->comment('访问终端');
            $table->string('last_ip', 45)->comment('访问ip');
            $table->timestamps();

            $table->index(['uid', 'entid'], 'entid_uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_log_0');
    }
};
