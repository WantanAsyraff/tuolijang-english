<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_token', function (Blueprint $table) {
            if (! Schema::hasColumn('user_token', 'refresh_token_hash')) {
                $table->string('refresh_token_hash', 64)->nullable()->after('remember_token')->comment('刷新TOKEN哈希');
            }
            if (! Schema::hasColumn('user_token', 'refresh_expires_at')) {
                $table->dateTime('refresh_expires_at')->nullable()->after('refresh_token_hash')->comment('刷新TOKEN失效时间');
            }
            if (! Schema::hasColumn('user_token', 'refresh_last_used_at')) {
                $table->dateTime('refresh_last_used_at')->nullable()->after('refresh_expires_at')->comment('刷新TOKEN最后使用时间');
            }
            if (! Schema::hasColumn('user_token', 'refresh_revoked_at')) {
                $table->dateTime('refresh_revoked_at')->nullable()->after('refresh_last_used_at')->comment('刷新TOKEN撤销时间');
            }
            if (! $this->indexExists('idx_user_token_refresh_hash')) {
                $table->index('refresh_token_hash', 'idx_user_token_refresh_hash');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_token', function (Blueprint $table) {
            if ($this->indexExists('idx_user_token_refresh_hash')) {
                $table->dropIndex('idx_user_token_refresh_hash');
            }
            $columns = array_values(array_filter([
                'refresh_token_hash',
                'refresh_expires_at',
                'refresh_last_used_at',
                'refresh_revoked_at',
            ], fn ($column) => Schema::hasColumn('user_token', $column)));
            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }

    private function indexExists(string $index): bool
    {
        $table = DB::getTablePrefix() . 'user_token';
        $index = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }
};
