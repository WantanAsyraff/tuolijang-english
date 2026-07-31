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
        Schema::create('frame_assist', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('辅助表自增id');
            $table->unsignedInteger('entid')->default(0)->comment('企业ID');
            $table->integer('frame_id')->default(0)->index('eb_enterprise_frame_assist_frame_id_index')->comment('主表(enterprise_frame)ID');
            $table->integer('user_id')->default(0)->index('eb_enterprise_frame_assist_user_id_index')->comment('副表(user_enterprise)ID');
            $table->tinyInteger('is_mastart')->default(0)->comment('是否为主部门');
            $table->boolean('is_admin')->default(false)->comment('是否为该部门的主管');
            $table->unsignedInteger('superior_uid')->default(0)->comment('上级主管用户ID');
            $table->timestamp('created_at')->nullable()->comment('添加时间');

            $table->index(['frame_id', 'user_id'], 'frame_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('frame_assist');
    }
};
