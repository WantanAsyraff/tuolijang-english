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
        Schema::table('attendance_statistics_leave', function (Blueprint $table) {
            $table->integer('holiday_type_id')->default(0)->comment('假期类型ID')->after('leave_duration');
            $table->decimal('leave_duration', 5)->default(0.0)->comment('请假工时')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_statistics_leave', function (Blueprint $table) {
            $table->dropColumn('holiday_type_id');
        });
    }
};
