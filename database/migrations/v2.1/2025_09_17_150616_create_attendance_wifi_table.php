<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::create('attendance_wifi', function (Blueprint $table) {
            $table->id();
            $table->integer('entid')->default('0')->comment('企业ID');
            $table->integer('group_id')->default('0')->comment('考勤组ID');
            $table->string('name', 64)->default('')->comment('wifi名称');
            $table->string('mac', 64)->default('')->comment('wifi地址');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('attendance_wifi');
    }
};
