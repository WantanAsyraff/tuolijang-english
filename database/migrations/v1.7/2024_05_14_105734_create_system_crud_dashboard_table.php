<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('system_crud_dashboard', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('user_id')->default(0)->comment('创建用户ID');
            $table->unsignedInteger('update_user_id')->default(0)->comment('修改用户ID');
            $table->string('name', 120)->default('')->comment('名称');
            $table->longText('configure')->default('')->comment('布局');
            $table->tinyInteger('status')->default(0)->comment('状态：0、关闭；1、开启；');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('system_crud_dashboard');
    }
};
