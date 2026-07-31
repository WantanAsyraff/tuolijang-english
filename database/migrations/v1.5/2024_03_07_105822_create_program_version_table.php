<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program_version', function (Blueprint $table) {
            $table->id()->comment('自增id');
            $table->bigInteger('program_id')->comment('项目ID');
            $table->string('name', 50)->default('')->comment('版本名称');
            $table->bigInteger('creator_uid')->default(0)->comment('创建人ID');
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
        Schema::dropIfExists('program_version');
    }
};
