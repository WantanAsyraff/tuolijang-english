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
        Schema::create('promotion_data', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedInteger('promotion_id')->comment('晋升表ID');
            $table->string('position', 50)->default('')->comment('职位');
            $table->decimal('total', 10)->default(0)->comment('合计');
            $table->string('benefit')->default('')->comment('效益工资');
            $table->longText('standard')->default('')->comment('标准');
            $table->integer('sort')->default(0)->comment('排序');
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
        Schema::dropIfExists('promotion_data');
    }
};
