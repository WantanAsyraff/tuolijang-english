<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('openapi_key', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('uid')->default(0)->comment('创建用户ID');
            $table->string('title', 50)->default('')->comment('对外接口名称')->unique()->index();
            $table->string('ak', 32)->default('')->comment('对外接口AK')->unique()->index();
            $table->string('sk', 64)->default('')->comment('对外接口SK');
            $table->string('info', 256)->default('')->comment('描述');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1、启用；0、禁用；');
            $table->timestamp('last_time')->nullable()->comment('最近登录时间');
            $table->string('last_ip', 32)->default('')->comment('最近登录IP');
            $table->text('auth')->default('')->comment('接口权限ID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('openapi_key');
    }
};
