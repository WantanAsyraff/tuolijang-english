<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['admin', 'user'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'language')) {
                $physicalTable = DB::getTablePrefix() . $table;
                DB::statement("ALTER TABLE `{$physicalTable}` MODIFY `language` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en' COMMENT '语言'");
            }
        }
    }

    public function down(): void
    {
        foreach (['admin', 'user'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'language')) {
                $physicalTable = DB::getTablePrefix() . $table;
                DB::statement("ALTER TABLE `{$physicalTable}` MODIFY `language` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'zh-cn' COMMENT '语言'");
            }
        }
    }
};