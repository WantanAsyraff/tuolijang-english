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
        Schema::create('enterprise_target', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_target_entid_foreign');
            $table->char('uid', 36)->index('eb_enterprise_target_uid_foreign');
            $table->integer('cate_id')->default(0)->comment('分类ID');
            $table->string('name', 256)->default('')->comment('指标名称');
            $table->text('content')->comment('指标内容');
            $table->integer('status')->default(0)->comment('开放状态：0、不开放；1、开放；');
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
        Schema::dropIfExists('enterprise_target');
    }
};
