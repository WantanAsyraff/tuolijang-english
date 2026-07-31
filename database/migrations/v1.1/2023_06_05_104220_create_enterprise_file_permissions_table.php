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
        Schema::create('enterprise_file_permissions', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('user_id')->default(0);
            $table->string('file_id', 32)->comment('文件标识');
            $table->char('uid', 36)->index('eb_enterprise_file_permissions_uid_foreign');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_file_permissions_entid_foreign');
            $table->enum('type', ['write', 'read']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprise_file_permissions');
    }
};
