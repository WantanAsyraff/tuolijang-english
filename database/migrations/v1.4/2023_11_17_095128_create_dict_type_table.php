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
        Schema::create('dict_type', function (Blueprint $table) {
            $table->id();
            $table->string('name',256)->nullable()->comment('字典名称');
            $table->string('ident',256)->nullable()->comment('字典标识');
            $table->string('link_type',32)->default('custom')->comment('关联业务');
            $table->unsignedTinyInteger('level')->default(4)->comment('数据最大层级');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态：1、开启；0、关闭；');
            $table->unsignedTinyInteger('is_default')->default(0)->comment('是否默认：1、是；0、否；');
            $table->string('mark',256)->nullable()->comment('备注信息');
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
        Schema::dropIfExists('dict_type');
    }
};
