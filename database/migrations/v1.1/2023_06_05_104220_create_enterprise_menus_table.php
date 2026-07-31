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
        Schema::create('enterprise_menus', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('菜单自增id');
            $table->unsignedBigInteger('menu_id')->index('eb_enterprise_menus_menu_id_foreign');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_menus_entid_foreign');
            $table->tinyInteger('type')->default(0)->comment('1=权限0=菜单');
            $table->tinyInteger('is_show')->default(1)->comment('是否为隐藏菜单供前台使用');
            $table->tinyInteger('status')->default(1)->comment('菜单状态 1=开启,0=关闭');
            $table->timestamps();
            $table->softDeletes()->comment('删除时间');

            $table->index(['status', 'entid'], 'is_admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_menus');
    }
};
