<?php

declare(strict_types=1);


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assess_plan', function (Blueprint $table) {
            if (! Schema::hasColumn('assess_plan', 'test_frame')) {
                $table->json('test_frame')->nullable()->comment('考核部门ID列表')->after('assess_type');
            }
            if (! Schema::hasColumn('assess_plan', 'test_user')) {
                $table->json('test_user')->nullable()->comment('考核人员ID列表')->after('test_frame');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assess_plan', function (Blueprint $table) {
            $columns = array_values(array_filter(['test_frame', 'test_user'], fn ($column) => Schema::hasColumn('assess_plan', $column)));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
