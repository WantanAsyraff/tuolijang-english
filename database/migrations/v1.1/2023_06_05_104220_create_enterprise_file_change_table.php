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
        Schema::create('enterprise_file_change', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('file_id', 32)->comment('文件标识');
            $table->integer('version')->default(0)->comment('文件版本号');
            $table->integer('user_id')->default(0)->comment('用户id(user_enterprise自增ID)');
            $table->string('change_message')->default('')->comment('变动说明');
            $table->timestamp('change_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_file_change');
    }
};
