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
        Schema::create('assess_compute_rule', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->string('name', 32)->default('')->comment('名称');
            $table->integer('scheme_id')->default(0)->index('eb_enterprise_assess_compute_rule_scheme_id_index')->comment('方案(enterprise_performance_scheme主键)id');
            $table->integer('row')->default(2)->comment('行数');
            $table->string('col', 10)->default('A')->comment('列数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assess_compute_rule');
    }
};
