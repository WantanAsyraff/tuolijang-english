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
        Schema::create('user_quick', function (Blueprint $table) {
            $table->comment('用户快捷入口');
            $table->increments('id')->comment('自增ID');
            $table->unsignedInteger('entid')->default(0)->comment('企业id');
            $table->string('uuid', 32)->default('0')->comment('用户uid');
            $table->string('pc_menu_id', 512)->default('')->comment('pc端菜单Id');
            $table->string('app_menu_id', 512)->default('')->comment('app端菜单Id');
            $table->string('statistics_type', 255)->default('')->comment('统计类型');
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
        Schema::dropIfExists('user_quick');
    }
};
