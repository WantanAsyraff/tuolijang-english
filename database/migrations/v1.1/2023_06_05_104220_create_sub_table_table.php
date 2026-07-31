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
        Schema::create('sub_table', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->string('table_name')->default('0')->index()->comment('表名');
            $table->string('sub_table_name', 32)->default('')->index()->comment('目前表名');
            $table->unsignedInteger('num')->default(0)->comment('目前表名增产');
            $table->unsignedInteger('count')->default(0)->comment('当前表数据条数');
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
        Schema::dropIfExists('sub_table');
    }
};
