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
        Schema::create('hay_group', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->string('name', 50)->default('')->comment('评估表名称');
            $table->integer('uid')->comment('业务员ID');
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
        Schema::dropIfExists('hay_group');
    }
};
