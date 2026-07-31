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
        Schema::create('system_crud_curl', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('title', 100)->default('')->comment('接口标题');
            $table->tinyInteger('is_pre')->default(0)->comment('0=直接请求，1=前置请求');
            $table->string('pre_url', 500)->default('')->comment('前置请求地址');
            $table->string('pre_method', 10)->default('post')->comment('前置请求method');
            $table->longText('pre_headers')->comment('前置请求header');
            $table->longText('pre_data')->comment('前置请求data');
            $table->integer('pre_cache_time')->default(0)->comment('前置请求缓存时间');
            $table->string('url', 500)->default('')->comment('请求地址');
            $table->string('method', 10)->default('post')->comment('请求method');
            $table->longText('headers')->comment('请求header');
            $table->longText('data')->comment('请求data');

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
