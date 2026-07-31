<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * 运行迁移.
     */
    public function up()
    {
        Schema::create('work_department', function (Blueprint $table) {
            $table->id();
            $table->string('corp_id', 18)->default('')->comment('企业id');
            $table->integer('department_id')->default('0')->comment('部门id');
            $table->string('name', 100)->default('')->comment('部门名称');
            $table->string('name_en', 50)->default('')->comment('部门英文名称');
            $table->string('department_leader', 1000)->default('')->comment('leader');
            $table->integer('parentid')->default('0')->comment('上级id');
            $table->integer('sort')->default('0')->comment('排序');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业微信组织架构');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_department');
    }
};
