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
        Schema::create('agreement', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('ident', 50)->default('')->comment('协议标识');
            $table->string('title', 50)->default('')->comment('协议标题');
            $table->longText('content')->comment('协议内容');
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
        Schema::dropIfExists('agreement');
    }
};
