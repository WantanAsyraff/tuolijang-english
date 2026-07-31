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
        Schema::create('system_crud_field', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('crud_id')->default(0)->index()->comment('关联CRUD_ID');
            $table->string('field_name', 100)->default('')->comment('字段中文名');
            $table->string('field_name_en', 100)->index()->default('')->comment('字段英文名');
            $table->string('form_value', 100)->default('')->comment('表单值类型');
            $table->string('field_type', 100)->default('')->comment('字段类型');
            $table->tinyInteger('is_default_value_not_null')->default(0)->comment('是否允许空值');
            $table->tinyInteger('is_table_show_row')->default(0)->comment('是否在列表中默认显示');
            $table->string('comment', 200)->default('')->comment('字段说明');
            $table->string('prev_field', 100)->default('')->comment('前一个字段英文名');
            $table->integer('data_dict_id')->default(0)->comment('数据字典ID');
            $table->integer('association_crud_id')->default(0)->comment('一对一关联CRUD_ID');
            $table->tinyInteger('is_main')->default(0)->comment('主展示字段');
            $table->tinyInteger('is_form')->default(0)->comment('是否展示在表单中');
            $table->string('form_field_uniqid', 200)->default('')->comment('表单字段唯一值');
            $table->text('association_field_names')->comment('一对一关联字段展示');
            $table->text('options')->comment('其他表单信息');
            $table->tinyInteger('create_modify')->default(1)->comment('是否创建时可以修改');
            $table->tinyInteger('update_modify')->default(1)->comment('是否修改时可以修改');
            $table->tinyInteger('is_default')->default(0)->comment('是否默认字段');

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
        Schema::dropIfExists('system_crud_field');
    }
};
