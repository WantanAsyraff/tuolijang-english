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
        Schema::create('user_remind_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('week')->default(0)->comment('当年的第几周');
            $table->integer('month')->default(0)->comment('当年的第几月');
            $table->integer('day')->default(0)->comment('当月的第几天');
            $table->integer('year')->default(0)->comment('那一年');
            $table->integer('quarter')->default(0)->comment('第几季度');
            $table->string('remind_type', 60)->default('')->comment('提醒类型');
            $table->integer('user_id')->default(0)->comment('user_enterprise表ID');
            $table->integer('relation_id')->default(0)->comment('关联id');
            $table->timestamps();

            $table->index(['year', 'entid', 'user_id', 'remind_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_remind_log');
    }
};
