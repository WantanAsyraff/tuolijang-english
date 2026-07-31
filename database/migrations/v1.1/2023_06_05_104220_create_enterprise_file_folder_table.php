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
        Schema::create('enterprise_file_folder', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('name', 100)->default('')->comment('文件夹名称');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_file_folder_entid_foreign');
            $table->integer('pid')->default(0)->comment('上级文件夹ID');
            $table->string('path')->default('')->comment('路径');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_file_folder');
    }
};
