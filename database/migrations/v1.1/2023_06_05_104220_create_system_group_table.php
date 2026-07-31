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
        Schema::create('system_group', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->integer('cate_id')->default(0)->comment('分类id');
            $table->string('group_key', 50)->default('1')->comment('数据字段英文名');
            $table->string('group_name', 50)->default('')->comment('数据字段中文名称');
            $table->string('group_info')->default('')->comment('数据字段提示');
            $table->text('fields')->comment('数据组字段以及类型（json数据）');
            $table->integer('entid')->default(0)->comment('商家ID：0、总平台');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();

            $table->index(['cate_id', 'group_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_group');
    }
};
