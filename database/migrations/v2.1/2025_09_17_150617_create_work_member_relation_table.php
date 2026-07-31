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
        Schema::create('work_member_relation', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id')->comment('员工ID');
            $table->integer('department')->comment('所属部门');
            $table->integer('srot')->comment('排序');
            $table->integer('is_leader_in_dept')->comment('是否为部门负责人');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('企业成员关联表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('work_member_relation');
    }
};
