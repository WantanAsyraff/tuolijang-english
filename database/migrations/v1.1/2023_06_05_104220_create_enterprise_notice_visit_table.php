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
        Schema::create('enterprise_notice_visit', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('user_id', 36)->default('')->index('eb_enterprise_notice_visit_uuid_index')->comment('创建用户ID');
            $table->unsignedBigInteger('notice_id')->index('eb_enterprise_notice_visit_notice_id_foreign');
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
        Schema::dropIfExists('enterprise_notice_visit');
    }
};
