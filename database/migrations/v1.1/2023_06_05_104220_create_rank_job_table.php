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
        Schema::create('rank_job', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_rank_job_entid_foreign');
            $table->string('name')->default('')->comment('职位名称');
            $table->unsignedBigInteger('cate_id')->index('eb_enterprise_rank_job_cate_id_foreign');
            $table->unsignedBigInteger('rank_id')->index('eb_enterprise_rank_job_rank_id_foreign');
            $table->unsignedBigInteger('card_id')->index('eb_enterprise_rank_job_card_id_foreign');
            $table->integer('job_count')->default(0)->comment('岗位人数');
            $table->string('describe')->default('')->comment('岗位描述');
            $table->mediumText('duty')->comment('岗位职责');
            $table->tinyInteger('status')->default(0)->comment('状态:0=关闭;1=开启');
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
        Schema::dropIfExists('rank_job');
    }
};
