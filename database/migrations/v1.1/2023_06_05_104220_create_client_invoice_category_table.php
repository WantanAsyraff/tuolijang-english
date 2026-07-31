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
        Schema::create('client_invoice_category', function (Blueprint $table) {
            $table->comment('发票类目');
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedBigInteger('entid');
            $table->string('name', 50)->default('')->comment('类目名称');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('client_invoice_category');
    }
};
