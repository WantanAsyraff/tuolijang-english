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
        Schema::create('client_labels', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('eid')->default(0)->comment('客户ID');
            $table->integer('label_id')->default(0)->comment('标签ID');
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
        Schema::dropIfExists('client_labels');
    }
};
