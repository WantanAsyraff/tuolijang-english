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
        Schema::create('program_task_comment', function (Blueprint $table) {
            $table->id()->comment('自增id');
            $table->bigInteger('task_id')->comment('任务ID');
            $table->bigInteger('pid')->comment('父级ID');
            $table->bigInteger('reply_uid')->comment('回复评论人ID');
            $table->bigInteger('uid')->comment('评论人ID');
            $table->text('describe')->default('')->comment('描述');
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
        Schema::dropIfExists('program_task_comment');
    }
};
