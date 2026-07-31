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
        Schema::create('assess_scheme', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增id');
            $table->unsignedBigInteger('entid')->index('eb_enterprise_assess_scheme_entid_foreign');
            $table->string('name', 50)->default('')->comment('名称');
            $table->tinyInteger('period')->default(0)->comment('周期:1=周;2=月;3=年');
            $table->enum('create_type', ['time', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->default('time')->comment('生成绩效日期类型');
            $table->integer('create_month')->default(0)->comment('生成绩效月份');
            $table->integer('create_day')->default(0)->comment('生成绩效日期');
            $table->string('create_time', 8)->default('')->comment('生成绩效时间');
            $table->enum('own_appraise_period', ['year', 'nextyear', 'month', 'nextmonth', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->default('year')->comment('自评结束时间类型');
            $table->integer('own_appraise_month')->default(0)->comment('自评结束月份');
            $table->integer('own_appraise_day')->default(0)->comment('自评结束日期');
            $table->string('own_appraise_time', 8)->default('')->comment('自评结束时间');
            $table->enum('leader_appraise_period', ['year', 'nextyear', 'month', 'nextmonth', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->default('year')->comment('上级评分结束时间类型');
            $table->integer('leader_appraise_month')->default(0)->comment('上级评分结束月份');
            $table->integer('leader_appraise_day')->default(0)->comment('上级评分结束日期');
            $table->string('leader_appraise_time', 8)->default('')->comment('上级评分结束时间');
            $table->integer('user_id')->index('eb_enterprise_assess_scheme_user_id_index')->comment('企业成员ID(user_enterprise主键ID)');
            $table->integer('user_count')->default(0)->comment('被考核人数');
            $table->string('file_id', 32)->comment('文件标识');
            $table->tinyInteger('status')->default(0)->comment('状态:0=禁用;1=开启');
            $table->timestamp('delete')->nullable()->comment('是否删除');
            $table->text('other')->comment('其他数据');
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
        Schema::dropIfExists('assess_scheme');
    }
};
