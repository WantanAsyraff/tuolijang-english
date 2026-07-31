<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_report_member', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->Integer('daily_id')->comment('汇报ID');
            $table->integer('member')->default(0)->comment('汇报人ID(user_enterprise自增ID)');
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
        Schema::dropIfExists('daily_report_member');
    }
};
