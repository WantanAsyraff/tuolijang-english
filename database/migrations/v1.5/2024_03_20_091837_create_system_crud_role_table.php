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
        Schema::create('system_crud_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('role_id')->default(0)->comment('关联角色ID');
            $table->unsignedInteger('crud_id')->default(0)->comment('关联实体ID');
            $table->string('crud_name',256)->default('')->nullable()->comment('关联实体名称');
            $table->unsignedTinyInteger('created')->default(0)->comment('新增权限');
            $table->unsignedInteger('reade')->default(0)->comment('查看权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许');
            $table->string('reade_frame',256)->nullable()->comment('查看部门');
            $table->unsignedInteger('updated')->default(0)->comment('修改权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许');
            $table->string('updated_frame',256)->nullable()->comment('可修改部门');
            $table->unsignedInteger('deleted')->default(0)->comment('删除权限:4、全部.3、指定部门.2、当前部门.1、仅本人.0、不允许');
            $table->string('deleted_frame',256)->nullable()->comment('可删除部门');
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
        Schema::dropIfExists('system_crud_role');
    }
};
