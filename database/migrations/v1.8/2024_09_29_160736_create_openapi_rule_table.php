<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('openapi_rule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pid')->default(0)->comment('上级id');
            $table->string('name')->default('')->comment('权限名称');
            $table->tinyInteger('type')->default(0)->comment('0=分类，1=接口');
            $table->integer('crud_id')->default(0)->comment('实体id');
            $table->string('method', 10)->default('')->comment('请求方式');
            $table->string('url')->default('')->comment('请求地址');
            $table->text('path_prams')->comment('请求参数');
            $table->timestamps();
            $table->text('get_prams')->nullable();
            $table->text('post_prams')->nullable();
            $table->text('request_data')->nullable();
            $table->text('response_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('openapi_key');
    }
};
