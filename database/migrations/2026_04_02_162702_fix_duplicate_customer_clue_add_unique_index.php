<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 修复线索重复数据并添加唯一索引
 *
 * 业务规则：
 * 1. 同一个企微客户 + 不同员工 → 可以有多条线索
 * 2. 同一个企微客户 + 同一员工 → 只能有一条活跃线索
 *
 * 唯一索引使用 deleted_at 为 NULL 作为条件，软删除的记录不受约束
 * MySQL 唯一索引对 NULL 值的处理：多个 NULL 值不违反唯一约束
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. 清理重复数据（同一员工 + 同一企微客户有多条活跃线索）
        // 保留每组中 id 最小的记录，其余软删除
        $duplicates = DB::table('customer_clue')
            ->select('external_userid', 'userid', DB::raw('COUNT(*) as count'))
            ->where('external_userid', '<>', '')
            ->whereNotNull('external_userid')
            ->where('userid', '<>', '')
            ->whereNotNull('userid')
            ->whereNull('deleted_at')
            ->groupBy('external_userid', 'userid')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            // 获取该组所有记录 ID，按 id 升序
            $ids = DB::table('customer_clue')
                ->where('external_userid', $duplicate->external_userid)
                ->where('userid', $duplicate->userid)
                ->whereNull('deleted_at')
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            if (count($ids) > 1) {
                // 保留第一条，软删除其余的
                $keepId = array_shift($ids);
                DB::table('customer_clue')
                    ->whereIn('id', $ids)
                    ->update(['deleted_at' => now()]);
            }
        }

        // 2. 删除现有的普通索引
        Schema::table('customer_clue', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('customer_clue');
            foreach ($indexes as $index) {
                $columns = $index->getColumns();
                if (count($columns) === 2 && in_array('external_userid', $columns) && in_array('userid', $columns)) {
                    $table->dropIndex($index->getName());
                    break;
                }
            }
        });

        // 3. 添加唯一索引
        // MySQL 的唯一索引对 NULL 值不生效，所以 deleted_at 为 NULL 时唯一约束生效
        Schema::table('customer_clue', function (Blueprint $table) {
            $table->unique(['external_userid', 'userid'], 'uk_customer_clue_external_userid_userid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_clue', function (Blueprint $table) {
            $table->dropUnique('uk_customer_clue_external_userid_userid');
            $table->index(['external_userid', 'userid']);
        });
    }
};
