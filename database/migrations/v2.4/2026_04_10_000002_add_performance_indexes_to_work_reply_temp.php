<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_reply_temp', function (Blueprint $table) {
            if (! Schema::hasColumn('work_reply_temp', 'is_personal')) {
                $table->tinyInteger('is_personal')->default(0)->index()->comment('是否个人库: 0=公共, 1=个人');
            }
            if (! Schema::hasColumn('work_reply_temp', 'source_id')) {
                $table->unsignedInteger('source_id')->default(0)->index()->comment('克隆来源ID');
            }
            if (! Schema::hasColumn('work_reply_temp', 'sync_time')) {
                $table->timestamp('sync_time')->nullable()->comment('最后同步时间');
            }
            // 复合索引：用于个人库列表查询 (uid + is_personal)
            if (! $this->indexExists('idx_uid_is_personal')) {
                $table->index(['uid', 'is_personal'], 'idx_uid_is_personal');
            }
            // 复合索引：用于公共库排序查询 (is_personal + sort)
            if (! $this->indexExists('idx_sort')) {
                $table->index(['is_personal', 'sort'], 'idx_sort');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_reply_temp', function (Blueprint $table) {
            if ($this->indexExists('work_reply_temp_is_personal_index')) {
                $table->dropIndex(['is_personal']);
            }
            if ($this->indexExists('work_reply_temp_source_id_index')) {
                $table->dropIndex(['source_id']);
            }
            if ($this->indexExists('idx_uid_is_personal')) {
                $table->dropIndex('idx_uid_is_personal');
            }
            if ($this->indexExists('idx_sort')) {
                $table->dropIndex('idx_sort');
            }
            $columns = array_values(array_filter(['is_personal', 'source_id', 'sync_time'], fn ($column) => Schema::hasColumn('work_reply_temp', $column)));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }

    private function indexExists(string $index): bool
    {
        $table = DB::getTablePrefix() . 'work_reply_temp';
        $index = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }
};
