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
        Schema::create('system_crud_form', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->integer('version')->default(0)->comment('版本号');
            $table->longText('options')->comment('表单信息');
            $table->longText('fields')->comment('表单字段信息');
            $table->longText('global_options')->comment('表单公共信息');
            $table->tinyInteger('is_index')->default(0)->comment('是否主表单');

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
        Schema::dropIfExists('system_crud_form');
    }
};
