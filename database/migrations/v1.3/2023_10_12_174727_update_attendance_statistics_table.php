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
        Schema::table('attendance_statistics', function (Blueprint $table) {
            $table->decimal('required_work_hours', 5)->default(0.0)->comment('应出勤工时')->change();
            $table->decimal('actual_work_hours', 5)->default(0.0)->comment('实际出勤工时')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_statistics', function (Blueprint $table) {
            //
        });
    }
};
