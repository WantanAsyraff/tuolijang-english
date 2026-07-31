<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const JSON_ARRAY_INDEXES = [
        'customer' => [
            'area_cascade',
            'customer_label',
            'member',
        ],
        'customer_clue' => [
            'area_cascade',
            'customer_label',
        ],
        'contract' => [
            'area_cascade',
            'contract_category',
        ],
        'contract_doc' => [
            'cid',
            'oid',
        ],
    ];

    /**
     * 为客户模块 JSON 数组列创建 Multi-Valued Index.
     *
     * 优化说明：
     * MySQL 8.0.17+ 支持对 JSON 数组创建 Multi-Valued Index，
     * 可将 JSON_CONTAINS 查询从全表扫描优化为索引查找。
     * 使用 CHAR(20) ARRAY 直接索引字符串值，无需数据转换。
     */
    public function up(): void
    {
        if (! $this->supportsJsonArrayIndex()) {
            return;
        }

        foreach (self::JSON_ARRAY_INDEXES as $table => $columns) {
            foreach ($columns as $column) {
                $this->addJsonArrayIndex($table, $column);
            }
        }
    }

    /**
     * 回滚迁移.
     */
    public function down(): void
    {
        if (! $this->supportsJsonArrayIndex()) {
            return;
        }

        foreach (self::JSON_ARRAY_INDEXES as $table => $columns) {
            foreach ($columns as $column) {
                $this->dropJsonArrayIndex($table, $column);
            }
        }
    }

    private function addJsonArrayIndex(string $table, string $column): void
    {
        if (! Schema::hasColumn($table, $column) || $this->columnType($table, $column) !== 'json') {
            return;
        }

        $index = $this->jsonArrayIndexName($column);
        if ($this->indexExists($table, $index) || ! $this->hasOnlyJsonScalarArrays($table, $column)) {
            return;
        }

        $fullTable = $this->table($table);
        $column    = $this->quoteIdentifier($column);
        DB::statement("ALTER TABLE `{$fullTable}` ADD INDEX `{$index}` ((CAST({$column} AS CHAR(20) ARRAY)))");
    }

    private function dropJsonArrayIndex(string $table, string $column): void
    {
        $index = $this->jsonArrayIndexName($column);
        if (! Schema::hasTable($table) || ! $this->indexExists($table, $index)) {
            return;
        }

        DB::statement("ALTER TABLE `{$this->table($table)}` DROP INDEX `{$index}`");
    }

    private function indexExists(string $table, string $index): bool
    {
        $fullTable = $this->table($table);
        $index     = DB::getPdo()->quote($index);

        return ! empty(DB::select("SHOW INDEX FROM `{$fullTable}` WHERE Key_name = {$index}"));
    }

    private function hasOnlyJsonScalarArrays(string $table, string $column): bool
    {
        $fullTable = $this->table($table);
        $column    = 't.' . $this->quoteIdentifier($column);

        $invalid = DB::selectOne(
            "SELECT 1 FROM `{$fullTable}` AS t
            WHERE {$column} IS NOT NULL
              AND (
                JSON_TYPE({$column}) <> 'ARRAY'
                OR EXISTS (
                    SELECT 1
                    FROM JSON_TABLE({$column}, '$[*]' COLUMNS (`item` JSON PATH '$')) AS jt
                    WHERE jt.`item` IS NULL
                       OR JSON_TYPE(jt.`item`) NOT IN ('INTEGER', 'DOUBLE', 'STRING', 'BOOLEAN')
                )
              )
            LIMIT 1"
        );

        return $invalid === null;
    }

    private function jsonArrayIndexName(string $column): string
    {
        return 'idx_' . $column;
    }

    private function supportsJsonArrayIndex(): bool
    {
        $version = (string) DB::selectOne('SELECT VERSION() AS version')->version;
        if (stripos($version, 'mariadb') !== false) {
            return false;
        }

        $version = preg_replace('/[^0-9.].*$/', '', $version) ?: '0.0.0';
        return version_compare($version, '8.0.17', '>=');
    }

    private function table(string $table): string
    {
        return DB::getTablePrefix() . $table;
    }

    private function columnType(string $table, string $column): string
    {
        $fullTable = $this->table($table);
        $column    = DB::getPdo()->quote($column);
        $result    = DB::selectOne("SHOW COLUMNS FROM `{$fullTable}` LIKE {$column}");

        return strtolower((string) ($result->Type ?? ''));
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
};
