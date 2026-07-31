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
        Schema::create('system_crud_comment', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('uid')->default(0)->comment('创建用户ID');
            $table->unsignedInteger('crud_id')->default(0)->comment('crud的主键id');
            $table->unsignedInteger('data_id')->default(0)->comment('crud的表的自增id');
            $table->unsignedInteger('pid')->default(0)->comment('评论父级id');
            $table->text('comment')->comment('评论内容');
            $table->timestamps();

            $table->index(['crud_id', 'data_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_crud_comment');
    }
};
