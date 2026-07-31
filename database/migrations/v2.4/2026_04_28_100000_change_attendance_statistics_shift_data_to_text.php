<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * shift_data 字段存储班次 JSON 数据（含 rules 三次复制），
     * varchar(1023) 不足存储完整 JSON 导致数据被截断、解析失败返回 null，
     * 改为 text 类型（最大 65535 字符）以支持完整班次数据。
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('attendance_statistics', 'shift_data')) {
            Schema::table('attendance_statistics', function (Blueprint $table) {
                $table->text('shift_data')->comment('班次数据')->change();
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
        if (Schema::hasColumn('attendance_statistics', 'shift_data')) {
            Schema::table('attendance_statistics', function (Blueprint $table) {
                $table->string('shift_data', 1023)->comment('班次数据')->change();
            });
        }
    }
};
