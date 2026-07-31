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
        Schema::create('client_category', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_client_category_entid_foreign');
            $table->string('path')->default('')->comment('路径');
            $table->string('name', 120)->default('')->comment('分类名称');
            $table->integer('pid')->default(0)->comment('上级ID');
            $table->tinyInteger('types')->default(0)->comment('变动类型:1=收入,0=支出');
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
        Schema::dropIfExists('client_category');
    }
};
