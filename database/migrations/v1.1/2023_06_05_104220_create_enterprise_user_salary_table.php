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
        Schema::create('enterprise_user_salary', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->unsignedInteger('card_id')->default(0)->comment('企业用户名片ID');
            $table->unsignedDecimal('total', 11)->default(0)->comment('变更内容');
            $table->date('take_date')->nullable()->comment('生效时间');
            $table->string('content', 2000)->default('')->comment('变更内容');
            $table->string('mark', 300)->default('')->comment('变更原因');
            $table->unsignedInteger('link_id')->default(0)->comment('关联申请单ID');
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
        Schema::dropIfExists('enterprise_user_salary');
    }
};
