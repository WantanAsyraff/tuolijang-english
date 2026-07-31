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
        if (!Schema::hasColumn('system_crud_event', 'curl_id')) {
            Schema::table('system_crud_event', function (Blueprint $table) {
                $table->integer('curl_id')->default(0)->comment('接口管理id')->after('crud_approve_id');
            });
        }
        if (!Schema::hasColumn('system_crud_event', 'timer_type')) {
            Schema::table('system_crud_event', function (Blueprint $table) {
                $table->tinyInteger('timer_type')->default(0)->comment('周期类型:0=间隔秒数；1=间隔n分；2=间隔n小时；3=间隔n天；4=每天；5=每星期；6=每年')->after('timer');
            });
        }
        if (!Schema::hasColumn('system_crud_event', 'timer_options')) {
            Schema::table('system_crud_event', function (Blueprint $table) {
                $table->text('timer_options')->comment('执行周期配置详情')->after('options');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_crud_event', function (Blueprint $table) {
            $table->dropColumn('curl_id');
            $table->dropColumn('timer_type');
            $table->dropColumn('timer_options');
        });
    }
};
