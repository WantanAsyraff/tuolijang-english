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
        Schema::create('dict_data', function (Blueprint $table) {
            $table->id();
            $table->string('name',256)->nullable()->comment('数据名称');
            $table->string('value',256)->nullable()->comment('数据值');
            $table->unsignedInteger('pid')->default(0)->comment('上级数据ID');
            $table->unsignedInteger('type_id')->default(0)->comment('字典类型ID');
            $table->string('type_name',256)->nullable()->index()->comment('字典类型名称');
            $table->unsignedTinyInteger('level')->default(1)->comment('数据层级');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('dict_data');
    }
};
