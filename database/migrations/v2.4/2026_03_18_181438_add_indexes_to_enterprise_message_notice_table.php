<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * 添加联合索引优化消息查询性能
     * - idx_to_uid_is_read_cate_id: 用于未读消息统计查询
     * - idx_to_uid_cate_id_created_at: 用于获取最新消息查询
     */
    public function up(): void
    {
        Schema::table('enterprise_message_notice', function (Blueprint $table) {
            // 联合索引：用于未读消息统计 (WHERE to_uid = ? AND is_read = 0 AND cate_id IN (...))
            if (! $this->indexExists('idx_to_uid_is_read_cate_id')) {
                $table->index(['to_uid', 'is_read', 'cate_id'], 'idx_to_uid_is_read_cate_id');
            }

            // 联合索引：用于获取最新消息 (WHERE to_uid = ? AND cate_id IN (...) ORDER BY created_at DESC)
            if (! $this->indexExists('idx_to_uid_cate_id_created_at')) {
                $table->index(['to_uid', 'cate_id', 'created_at'], 'idx_to_uid_cate_id_created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enterprise_message_notice', function (Blueprint $table) {
            if ($this->indexExists('idx_to_uid_is_read_cate_id')) {
                $table->dropIndex('idx_to_uid_is_read_cate_id');
            }
            if ($this->indexExists('idx_to_uid_cate_id_created_at')) {
                $table->dropIndex('idx_to_uid_cate_id_created_at');
            }
        });
    }

    private function indexExists(string $index): bool
    {
        $table = DB::getTablePrefix() . 'enterprise_message_notice';
        $index = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }
};
//ALTER TABLE `eb_enterprise_message_notice` ADD INDEX( `to_uid`, `cate_id`, `is_read`);
//ALTER TABLE `eb_enterprise_message_notice` ADD INDEX( `to_uid`, `cate_id`, `created_at`);
