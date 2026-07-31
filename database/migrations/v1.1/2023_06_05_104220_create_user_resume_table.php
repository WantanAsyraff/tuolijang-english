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
        Schema::create('user_resume', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
            $table->char('uid', 36)->index('eb_user_resume_uid_foreign');
            $table->string('photo', 256)->default('')->comment('照片');
            $table->string('name', 32)->default('')->comment('员工姓名');
            $table->string('phone', 32)->default('')->comment('手机号');
            $table->string('position', 32)->default('')->comment('期望职位');
            $table->string('birthday', 32)->default('')->comment('生日');
            $table->string('nation', 32)->default('')->comment('种族');
            $table->string('politic', 32)->default('')->comment('政治面貌');
            $table->string('native', 32)->default('')->comment('籍贯');
            $table->string('address', 64)->default('')->comment('居住地');
            $table->tinyInteger('sex')->default(0)->comment('性别: 0、未知；1、男；2、女；');
            $table->unsignedTinyInteger('age')->default(18)->comment('年龄');
            $table->unsignedTinyInteger('marriage')->default(0)->comment('婚姻状况:0、未婚；1、已婚；');
            $table->tinyInteger('is_part')->default(0)->comment('是否兼职:1、是；0、否；');
            $table->unsignedTinyInteger('work_years')->default(0)->comment('工作年限');
            $table->string('spare_name', 32)->default('')->comment('紧急联系人');
            $table->string('spare_tel', 32)->default('')->comment('紧急联系电话');
            $table->string('email', 64)->default('')->comment('邮箱');
            $table->string('work_time', 32)->default('')->comment('入职时间');
            $table->string('trial_time', 32)->default('')->comment('试用时间');
            $table->string('formal_time', 32)->default('')->comment('转正时间');
            $table->string('treaty_time', 32)->default('')->comment('合同到期时间');
            $table->string('social_num', 32)->default('')->comment('社保账户');
            $table->string('fund_num', 32)->default('')->comment('公积金账户');
            $table->string('bank_num', 32)->default('')->comment('银行卡账户');
            $table->string('bank_name', 32)->default('')->comment('开户行');
            $table->string('graduate_name', 32)->default('')->comment('毕业院校');
            $table->string('graduate_date', 32)->default('')->comment('毕业时间');
            $table->string('card_id', 32)->default('')->comment('身份证号');
            $table->string('card_front', 256)->default('')->comment('身份证正面');
            $table->string('card_both', 256)->default('')->comment('身份证背面');
            $table->string('education', 32)->default('')->comment('学历');
            $table->string('education_image', 256)->default('')->comment('学历证书');
            $table->string('acad', 32)->default('')->comment('学位');
            $table->string('acad_image', 256)->default('')->comment('学位证书');
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
        Schema::dropIfExists('user_resume');
    }
};
