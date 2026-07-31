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
        Schema::create('customer_target', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->integer('uid')->default('0')->comment('用户ID');
            $table->integer('link_id')->default('0')->comment('用户/部门ID');
            $table->decimal('amount', 11, 2)->default('0.00')->comment('目标额');
            $table->integer('year')->nullable()->comment('年份');
            $table->integer('month')->nullable()->comment('月份');
            $table->integer('types')->default('0')->comment('类型：0、人员；1、部门；');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->engine = 'InnoDB';
            $table->comment('业绩目标表');
        });
    }

    /**
     * 回滚迁移.
     */
    public function down()
    {
        Schema::dropIfExists('customer_target');
    }
};
