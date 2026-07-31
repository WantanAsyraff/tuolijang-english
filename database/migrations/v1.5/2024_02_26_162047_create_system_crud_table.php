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
        Schema::create('system_crud', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('table_name', 100)->default('')->comment('表中文名');
            $table->string('table_name_en', 100)->index()->default('')->comment('表英文名');
            $table->string('cate_ids', 500)->default('')->comment('分类IDS');
            $table->string('info', 500)->default('')->comment('说明');
            $table->integer('crud_id')->default(0)->comment('主表CRUD_ID；为空为主表');
            $table->integer('user_id')->default(0)->comment('创建者ID');
            $table->longText('form_fields')->comment('当前form选择中的字段集合');
            $table->tinyInteger('is_update_form')->default(0)->comment('是否允许修改表单');
            $table->tinyInteger('is_update_table')->default(0)->comment('是否允许修改表格');

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
        Schema::dropIfExists('system_crud');
    }
};
