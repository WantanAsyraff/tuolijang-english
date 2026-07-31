<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPassRatioToApproveProcessTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('system_crud_approve_process', 'pass_ratio')) {
            Schema::table('system_crud_approve_process', function (Blueprint $table) {
                $table->unsignedTinyInteger('pass_ratio')->default(0)->comment('通过比例(%) 0=关闭（使用原逻辑）');
            });
        }

        if (! Schema::hasColumn('approve_process', 'pass_ratio')) {
            Schema::table('approve_process', function (Blueprint $table) {
                $table->unsignedTinyInteger('pass_ratio')->default(0)->comment('通过比例(%) 0=关闭（使用原逻辑）');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('system_crud_approve_process', 'pass_ratio')) {
            Schema::table('system_crud_approve_process', function (Blueprint $table) {
                $table->dropColumn('pass_ratio');
            });
        }

        if (Schema::hasColumn('approve_process', 'pass_ratio')) {
            Schema::table('approve_process', function (Blueprint $table) {
                $table->dropColumn('pass_ratio');
            });
        }
    }
}
