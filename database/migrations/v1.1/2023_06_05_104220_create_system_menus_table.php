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
        Schema::create('system_menus', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('菜单自增id');
            $table->integer('pid')->default(0)->comment('上级菜单ID');
            $table->string('icon', 50)->default('')->comment('按钮图标');
            $table->string('menu_name', 60)->default('')->comment('按钮名');
            $table->string('api', 100)->default('')->index('api')->comment('api请求地址');
            $table->string('methods', 10)->default('')->comment('请求方式POST GET PUT DELETE');
            $table->string('unique_auth', 200)->default('')->comment('前台唯一标识');
            $table->string('menu_path')->default('')->comment('前端路由路径');
            $table->string('uni_path', 256)->default('')->comment('移动端路径');
            $table->string('uni_img', 256)->default('')->comment('移动端图标');
            $table->tinyInteger('position')->nullable()->default(0)->comment('菜单位置 0=侧方1=顶部');
            $table->string('path')->default('')->comment('路径');
            $table->unsignedTinyInteger('level')->default(0);
            $table->string('other', 2000)->default('')->comment('其他参数');
            $table->integer('sort')->default(1)->comment('排序');
            $table->integer('entid')->default(0)->comment('菜单归属 0=总后台');
            $table->tinyInteger('is_default')->default(0)->comment('是否为默认权限1是0否');
            $table->boolean('type')->default(false)->comment('1=权限0=菜单');
            $table->boolean('is_show')->default(true)->comment('是否为隐藏菜单供前台使用');
            $table->boolean('status')->default(true)->comment('菜单状态 1=开启,0=关闭');
            $table->timestamp('is_del')->nullable()->comment('是否删除');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('system_menus');
    }
};
