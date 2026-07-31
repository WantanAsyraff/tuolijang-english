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
        Schema::create('program_task', function (Blueprint $table) {
            $table->id()->comment('自增id');
            $table->string('name', 120)->default('')->comment('任务名称');
            $table->string('ident', 12)->default('')->comment('任务编号');
            $table->bigInteger('pid')->comment('父级ID');
            $table->string('path')->comment('路径');
            $table->bigInteger('top_id')->comment('顶级ID');
            $table->integer('level')->comment('级别');
            $table->bigInteger('program_id')->comment('项目ID');
            $table->bigInteger('version_id')->comment('版本ID');
            $table->bigInteger('creator_uid')->default(0)->comment('创建人ID');
            $table->bigInteger('uid')->default(0)->comment('负责人');
            $table->tinyInteger('status')->default(0)->comment('项目状态：0：未处理；1：进行中；2：已解决；3：已验收；4：已拒绝；');
            $table->tinyInteger('priority')->default(0)->comment('优先级：1：紧急；2：高；3：中；4：低；');
            $table->date('plan_start')->nullable()->comment('计划开始');
            $table->date('plan_end')->nullable()->comment('计划结束');
            $table->integer('sort')->default(0)->comment('排序');
            $table->text('describe')->default('')->comment('任务描述');
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
        Schema::dropIfExists('program_task');
    }
};
