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
        Schema::create('system_storage', function (Blueprint $table) {
            $table->comment('云储存');
            $table->integer('id', true)->comment('自增ID');
            $table->string('access_key', 100)->default('')->comment('access_key');
            $table->boolean('type')->default(true)->comment('1=本地存储,2=七牛,3=oss,4=cos');
            $table->string('name', 100)->default('')->comment('空间名');
            $table->string('region', 100)->default('')->comment('地域');
            $table->enum('acl', ['private', 'public-read', 'public-read-write'])->default('public-read')->comment('权限');
            $table->string('domain', 100)->default('')->comment('空间域名');
            $table->string('cdn')->default('')->comment('CDN加速域名');
            $table->string('cname')->default('')->comment('CNAME值');
            $table->boolean('is_ssl')->default(false)->comment('0=http,1=https');
            $table->boolean('status')->default(false)->index('status')->comment('状态');
            $table->boolean('is_delete')->default(false)->index('is_delete')->comment('是否删除');
            $table->integer('add_time')->comment('添加事件');
            $table->integer('update_time')->comment('更新事件');
            $table->timestamps();
            $table->index(['access_key', 'type', 'is_delete'], 'access_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_storage');
    }
};
