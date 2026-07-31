<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('program', function (Blueprint $table) {
            $table->id('id')->comment('id');
            $table->string('name', 120)->default('')->comment('名称');
            $table->string('ident', 18)->default('')->comment('编号');
            $table->bigInteger('uid')->default(0)->comment('负责人');
            $table->bigInteger('eid')->default(0)->comment('关联客户');
            $table->bigInteger('cid')->default(0)->comment('关联合同');
            $table->bigInteger('creator_uid')->default(0)->comment('创建人ID');
            $table->date('start_date')->nullable()->comment('开始时间');
            $table->date('end_date')->nullable()->comment('结束时间');
            $table->tinyInteger('status')->default(0)->comment('项目状态：0：正常；1：暂停；2：关闭；');
            $table->string('describe', 1000)->default('')->comment('项目描述');
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
        Schema::dropIfExists('program');
    }
};
