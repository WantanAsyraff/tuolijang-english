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
        Schema::create('system_crud_table', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->integer('version')->default(0)->comment('版本号');
            $table->longText('senior_search')->comment('高级搜索');
            $table->longText('view_search')->comment('视图搜索');
            $table->longText('show_field')->comment('默认展示字段搜索');
            $table->longText('options')->comment('其他表单信息');
            $table->tinyInteger('is_index')->default(0)->comment('是否主配置');

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
        Schema::dropIfExists('system_crud_table');
    }
};
