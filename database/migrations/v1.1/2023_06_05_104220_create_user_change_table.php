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
        Schema::create('user_change', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('用户信息变动自增id');
            $table->string('uuid', 36)->comment('用户uuid(关联user表uuid)');
            $table->string('type', 20)->default('')->comment('变动类型');
            $table->string('change_mesage')->default('')->comment('变动说明');
            $table->timestamp('change_time')->nullable()->comment('变动时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_change');
    }
};
