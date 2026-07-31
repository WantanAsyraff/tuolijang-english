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
        Schema::create('system_package', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('name', 32)->default('')->comment('扩展包名称');
            $table->string('info', 256)->default('')->comment('扩展包简介');
            $table->string('version', 32)->default('')->comment('扩展包版本');
            $table->string('file', 256)->default('')->comment('扩展包文件名');
            $table->string('path', 256)->default('')->comment('扩展包路径');
            $table->unsignedTinyInteger('status')->default(1)->comment('是否可用');
            $table->string('uniqued')->default('')->comment('校验码');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('system_package');
    }
};
