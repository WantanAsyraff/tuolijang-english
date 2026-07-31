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
        Schema::create('schedule_user', function (Blueprint $table) {
            $table->increments('id')->comment('主键ID');
            $table->unsignedInteger('uid')->default(0)->comment('企业用户ID');
            $table->unsignedInteger('schedule_id')->default(0)->comment('关联日程ID');
            $table->boolean('is_master')->default(false)->comment('是否为组织人0=否，1=是');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_user');
    }
};
