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
    public function up(): void
    {
        Schema::table('approve_holiday_type', function (Blueprint $table) {
            $table->unsignedInteger('sort')->default(0)->comment('排序')->after('duration_calc_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('approve_holiday_type', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
