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
        Schema::create('contract_resource', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('cid')->default(0)->comment('合同ID');
            $table->integer('uid')->default(0)->comment('副表(user_enterprise)ID');
            $table->text('content')->comment('备注内容');
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
        Schema::dropIfExists('contract_resource');
    }
};
