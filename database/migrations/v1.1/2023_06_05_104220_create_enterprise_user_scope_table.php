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
        Schema::create('enterprise_user_scope', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('uid')->default(0)->comment('用户ID');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedInteger('link_id')->default(0)->comment('关联ID');
            $table->unsignedTinyInteger('types')->default(0)->comment('0、组织架构；1、用户；');
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
        Schema::dropIfExists('enterprise_user_scope');
    }
};
