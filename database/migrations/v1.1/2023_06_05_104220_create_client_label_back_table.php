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
        Schema::create('client_label_back', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->string('name', 120)->default('')->comment('标签名称');
            $table->integer('sort')->default(0)->comment('排序');
            $table->integer('pid')->default(0)->comment('上级ID');
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
        Schema::dropIfExists('client_label_back');
    }
};
