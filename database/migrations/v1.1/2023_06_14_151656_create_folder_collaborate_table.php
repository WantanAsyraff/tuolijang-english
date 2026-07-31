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
        Schema::create('folder_collaborate', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('uid')->comment('业务员ID');
            $table->unsignedBigInteger('folder_id')->comment('文件ID');
            $table->tinyInteger('update')->default(0)->comment('更新权限');
            $table->string('uniqued')->default('')->comment('校验码');
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
        Schema::dropIfExists('folder_collaborate');
    }
};
