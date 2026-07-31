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
        Schema::create('enterprise_user_daily_reply', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('pid')->index('eb_enterprise_user_daily_reply_pid_foreign');
            $table->unsignedBigInteger('daily_id')->index('eb_enterprise_user_daily_reply_daily_id_foreign');
            $table->char('uid', 36)->index('eb_enterprise_user_daily_reply_uid_foreign');
            $table->string('content', 2000)->default('')->comment('回复内容');
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
        Schema::dropIfExists('enterprise_user_daily_reply');
    }
};
