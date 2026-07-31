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
        Schema::create('rank', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('name', 120)->default('')->comment('职级名称');
            $table->unsignedBigInteger('entid');
            $table->unsignedBigInteger('cate_id');
            $table->unsignedBigInteger('card_id');
            $table->string('alias', 128)->comment('职级别名');
            $table->string('info', 2000)->comment('职级描述');
            $table->integer('number')->default(0)->comment('职级人数');
            $table->tinyInteger('status')->default(0)->comment('状态:1=开启,0=关闭');
            $table->timestamps();
            $table->index(['entid', 'cate_id', 'status'], 'list');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rank');
    }
};
