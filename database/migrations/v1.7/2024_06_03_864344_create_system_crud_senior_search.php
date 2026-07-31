<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_crud_senior_search', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->integer('user_id')->default(0)->index()->comment('关联USER_ID');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('senior_title', 100)->default('')->comment('高级搜索标题');
            $table->longText('senior_search')->comment('高级搜索');
            $table->tinyInteger('senior_type')->default(0)->comment('0=个人，1=系统');
            $table->tinyInteger('search_boolean')->default(0)->comment('0=or，1=and');

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
        Schema::dropIfExists('system_crud_senior_search');
    }
};
