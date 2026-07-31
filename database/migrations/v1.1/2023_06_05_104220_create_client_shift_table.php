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
        Schema::create('client_shift', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedInteger('from')->default(0)->comment('客户ID');
            $table->unsignedInteger('to')->default(0)->comment('合同ID');
            $table->unsignedInteger('uid')->default(0)->comment('用户ID');
            $table->unsignedInteger('link_id')->default(0)->comment('关联ID');
            $table->unsignedTinyInteger('types')->default(0)->comment('类型：0、客户；1、合同；2、联系人；3、发票；');
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
        Schema::dropIfExists('client_shift');
    }
};
