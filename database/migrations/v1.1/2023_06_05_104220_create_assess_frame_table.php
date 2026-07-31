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
        Schema::create('assess_frame', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->tinyInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedBigInteger('planid')->index('eb_enterprise_assess_frame_planid_foreign');
            $table->integer('test_frame_id')->default(0)->comment('企业组织架构表');
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
        Schema::dropIfExists('assess_frame');
    }
};
