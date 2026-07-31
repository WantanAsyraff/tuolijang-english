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
        Schema::create('enterprise_template_collect', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('entid')->default(0)->comment('企业ID');
            $table->integer('user_id')->default(0)->comment('企业用户ID');
            $table->integer('temp_id')->default(0)->comment('考核模板ID');
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
        Schema::dropIfExists('enterprise_template_collect');
    }
};
