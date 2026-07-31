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
        Schema::create('system_crud_table_user', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->integer('user_id')->default(0)->index()->comment('关联USER_ID');
            $table->longText('senior_search')->comment('高级搜索');
            $table->longText('show_field')->comment('字段信息');
            $table->longText('options')->comment('其他信息');

            $table->index(['crud_id', 'user_id']);

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
        Schema::dropIfExists('system_crud_table_user');
    }
};
