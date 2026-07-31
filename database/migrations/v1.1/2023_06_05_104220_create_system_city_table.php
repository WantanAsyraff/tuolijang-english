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
        Schema::create('system_city', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('city_id')->default(0)->comment('城市ID');
            $table->integer('level')->default(0)->comment('省市级别');
            $table->integer('parent_id')->default(0)->comment('父级id');
            $table->string('area_code', 30)->default('')->comment('区号');
            $table->string('name', 100)->default('')->comment('名称');
            $table->string('merger_name')->default('')->comment('合并名称');
            $table->string('lng', 50)->default('')->comment('经度');
            $table->string('lat', 50)->default('')->comment('纬度');
            $table->tinyInteger('is_show')->default(0)->comment('是否展示');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_city');
    }
};
