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
        Schema::create('assess_space', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_assess_space_entid_foreign');
            $table->integer('assessid')->default(0)->comment('考核列表ID');
            $table->integer('targetid')->default(0)->comment('考核模板ID');
            $table->string('name', 64)->default('')->comment('维度名称');
            $table->integer('ratio')->default(0)->comment('维度占比');
            $table->softDeletes()->comment('删除时间');
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
        Schema::dropIfExists('assess_space');
    }
};
