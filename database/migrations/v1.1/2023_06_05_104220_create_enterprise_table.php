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
        Schema::create('enterprise', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('企业表自增id');
            $table->string('logo')->default('')->comment('公司logo');
            $table->string('title', 50)->default('')->comment('管理后台标题');
            $table->string('enterprise_name', 51)->default('')->comment('公司名称');
            $table->string('short_name', 12)->default('')->comment('公司简称');
            $table->string('enterprise_number', 30)->default('')->comment('公司编号');
            $table->string('enterprise_name_en')->default('')->comment('公司名称英文');
            $table->string('lead', 20)->default('')->comment('法人代表');
            $table->string('telephone', 20)->default('')->comment('电话号');
            $table->string('phone', 11)->default('')->comment('手机号');
            $table->string('province', 20)->default('')->comment('所在省');
            $table->string('city', 20)->default('')->comment('所在城市');
            $table->string('area', 20)->default('')->comment('所在区');
            $table->string('address')->default('')->comment('详细地址');
            $table->string('synopsis', 500)->default('')->comment('简介');
            $table->string('fax', 8)->default('')->comment('传真');
            $table->string('business_license')->default('')->comment('营业执照');
            $table->string('remark')->default('')->comment('备注');
            $table->string('disable_remark')->default('')->comment('禁用备注');
            $table->string('introduction', 2000)->default('')->comment('简介');
            $table->string('other', 500)->default('')->comment('其他');
            $table->string('uid', 36)->default('')->index('uid')->comment('所属用户');
            $table->integer('scale')->default(0)->comment('公司规模');
            $table->integer('type')->default(0)->comment('企业类型');
            $table->integer('level')->default(0)->comment('企业等级');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('verify')->default(0)->comment('0=审核,1=审核通过,-1=不通过');
            $table->unsignedTinyInteger('remind')->default(0)->comment('提醒状态：0、未读；1、已读；');
            $table->string('uniqued', 64)->default('')->comment('企业唯一值');
            $table->unsignedTinyInteger('init_data')->default(0)->comment('是否已加载默认数据');
            $table->bigInteger('disk_size')->default(0)->comment('已使用云盘空间');
            $table->tinyInteger('status')->default(0)->comment('0=禁用,1=正常,2=待缴费,3=已过期');
            $table->timestamp('delete')->nullable()->comment('是否删除');
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
        Schema::dropIfExists('enterprise');
    }
};
