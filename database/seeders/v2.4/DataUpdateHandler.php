<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DataUpdateHandler
{
    private const JSON_BATCH_SIZE = 500;

    public function __construct()
    {
        $this->addIndexes();
        $this->changeCustomerJsonColumnsToJson();
        $this->changeScheduleDaysToJson();
    }

    /**
     * 添加查询索引.
     */
    private function addIndexes(): void
    {
        $this->addIndex('customer_record', 'idx_eid_linktype_createdat', ['eid', 'link_type', 'created_at']);
        $this->addIndex('customer_clue', 'idx_userid_external_uid_deleted', ['userid', 'external_userid', 'uid', 'deleted_at']);
        $this->addIndex('schedule_user', 'idx_uid_schedule_id', ['uid', 'schedule_id']);
        $this->addIndex('schedule_user', 'idx_schedule_id', ['schedule_id']);
        $this->addIndex('work_group_chat_member', 'idx_group_id_userid', ['group_id', 'userid']);
    }

    /**
     * schedule.days 统一转为 JSON 类型.
     */
    private function changeScheduleDaysToJson(): void
    {
        if (! Schema::hasColumn('schedule', 'days')) {
            return;
        }
        if ($this->columnType('schedule', 'days') === 'json') {
            return;
        }

        $table = $this->table('schedule');
        DB::statement("ALTER TABLE `{$table}` CHANGE `days` `days` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '重复星期/日期'");
        $this->normalizeJsonArrayColumn('schedule', 'days');
        DB::statement("ALTER TABLE `{$table}` CHANGE `days` `days` JSON NULL COMMENT '重复星期/日期'");
    }

    /**
     * 客户模块中历史字符串 JSON 数组字段统一转为 JSON 类型.
     */
    private function changeCustomerJsonColumnsToJson(): void
    {
        $this->changeStringJsonArrayColumnToJson('customer', 'customer_label', '客户标签');
        $this->changeStringJsonArrayColumnToJson('customer', 'area_cascade', '省市区');
        $this->normalizeJsonArrayColumn('customer', 'member');
        $this->changeStringJsonArrayColumnToJson('customer_clue', 'customer_label', '客户标签');
        $this->changeStringJsonArrayColumnToJson('customer_clue', 'area_cascade', '省市区');
        $this->changeStringJsonArrayColumnToJson('contract', 'contract_category', '订单分类');
        $this->addCustomerJsonArrayIndexes();
    }

    private function changeStringJsonArrayColumnToJson(string $tableName, string $columnName, string $comment): void
    {
        if (! Schema::hasColumn($tableName, $columnName)) {
            return;
        }
        if ($this->columnType($tableName, $columnName) === 'json') {
            $this->normalizeJsonArrayColumn($tableName, $columnName);
            return;
        }

        $table  = $this->table($tableName);
        $column = $this->quoteIdentifier($columnName);

        DB::statement("ALTER TABLE `{$table}` CHANGE {$column} {$column} TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '{$comment}'");
        $this->normalizeJsonArrayColumn($tableName, $columnName);
        DB::statement("ALTER TABLE `{$table}` CHANGE {$column} {$column} JSON NULL COMMENT '{$comment}'");
    }

    private function normalizeJsonArrayColumn(string $tableName, string $columnName): void
    {
        if (! Schema::hasColumn($tableName, $columnName)) {
            return;
        }

        $lastId = 0;
        do {
            $rows = DB::table($tableName)
                ->select(['id', $columnName])
                ->where('id', '>', $lastId)
                ->whereNotNull($columnName)
                ->orderBy('id')
                ->limit(self::JSON_BATCH_SIZE)
                ->get();

            if ($rows->isEmpty()) {
                break;
            }

            $updates = [];
            foreach ($rows as $row) {
                $lastId   = (int) $row->id;
                $original = $row->{$columnName};
                try {
                    $fixed = $this->convertToStandardJsonArray($original);
                } catch (\Throwable) {
                    $fixed = null;
                }

                if ($fixed !== $original) {
                    $updates[$lastId] = $fixed;
                }
            }

            $this->bulkUpdateColumnById($tableName, $columnName, $updates);
        } while ($rows->count() === self::JSON_BATCH_SIZE);
    }

    private function bulkUpdateColumnById(string $tableName, string $columnName, array $updates): void
    {
        if (empty($updates)) {
            return;
        }

        $table       = $this->table($tableName);
        $column      = $this->quoteIdentifier($columnName);
        $cases       = [];
        $bindings    = [];
        $ids         = [];
        $placeholders = [];

        foreach ($updates as $id => $value) {
            $id              = (int) $id;
            $cases[]         = 'WHEN ? THEN ?';
            $bindings[]      = $id;
            $bindings[]      = $value;
            $ids[]           = $id;
            $placeholders[]  = '?';
        }

        $bindings = array_merge($bindings, $ids);
        $preserveCreatedAt = $tableName === 'customer' && Schema::hasColumn($tableName, 'created_at')
            ? ', `created_at` = `created_at`'
            : '';

        DB::update(
            "UPDATE `{$table}` SET {$column} = CASE `id` " . implode(' ', $cases) . " ELSE {$column} END{$preserveCreatedAt} WHERE `id` IN (" . implode(',', $placeholders) . ')',
            $bindings
        );
    }

    private function convertToStandardJsonArray($value): ?string
    {
        if (! is_string($value)) {
            $value = (string) $value;
        }

        $value = trim($value);
        if ($this->isNullLikeValue($value)) {
            return null;
        }

        if (str_contains($value, '/') && ! preg_match('/[\{\[\}]/', $value)) {
            $jsonData = array_values(array_filter(array_map('trim', explode('/', trim($value, '/'))), fn ($item) => $item !== ''));
        } elseif (preg_match('/^\[.*\]$/', $value)) {
            $jsonData = json_decode($value, true);
            if (! is_array($jsonData)) {
                $jsonData = [(string) trim($value, '[] ')];
            }
        } else {
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last  = substr($value, -1);
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = trim(substr($value, 1, -1));
                }
            }

            $unescaped = $this->unescapeValue($value);
            $jsonData  = json_decode($unescaped, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $jsonData = $this->tryDecodeLooseJson($unescaped);
            }
        }

        if (! is_array($jsonData)) {
            $jsonData = [(string) $jsonData];
        } elseif (! array_is_list($jsonData)) {
            return null;
        }

        $jsonData = $this->normalizeJsonArray($jsonData);

        if ($jsonData === []) {
            return null;
        }

        return json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    private function isNullLikeScalar($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (! is_scalar($value)) {
            return false;
        }

        $value = trim((string) $value);

        return $value === '' || in_array(strtolower($value), ['null', 'nil', 'n/a'], true);
    }

    private function isNullLikeValue(string $value): bool
    {
        $value = trim($value);

        if ($this->isNullLikeScalar($value) || in_array($value, ['[]', '{}', '""', "''", '[""]'], true)) {
            return true;
        }

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                return $this->isNullLikeScalar(substr($value, 1, -1));
            }
        }

        $decoded = json_decode($value, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if ($this->isNullLikeScalar($decoded)) {
            return true;
        }

        if (! is_array($decoded)) {
            return false;
        }

        foreach ($decoded as $item) {
            if (! $this->isNullLikeScalar($item)) {
                return false;
            }
        }

        return true;
    }

    private function normalizeJsonArray(array $items): array
    {
        $jsonData = [];

        foreach ($items as $item) {
            if ($this->isNullLikeScalar($item)) {
                continue;
            }

            if (is_bool($item)) {
                $jsonData[] = $item;
                continue;
            }

            if (! is_scalar($item)) {
                continue;
            }

            $jsonData[] = (string) $item;
        }

        return $jsonData;
    }

    private function tryDecodeLooseJson(string $value): array
    {
        $strategies = [
            fn (string $str) => str_replace("'", '"', $str),
            fn (string $str) => preg_replace('/([{,]\s*)(\w+)(\s*:)/', '$1"$2"$3', $str),
            fn (string $str) => preg_replace_callback('/(?<=[,\[]\s*)([^",\]]+?)(?=\s*[,\]])/', function ($match) {
                $val = trim($match[0]);
                return in_array(strtolower($val), ['true', 'false'], true) ? $val : '"' . addslashes($val) . '"';
            }, $str),
            function (string $str): string {
                if (str_contains($str, ',') && ! preg_match('/[\{\[\}]/', $str)) {
                    return '[' . implode(',', array_map(
                        fn ($part) => '"' . addslashes(trim($part, " \t\n\r\0\x0B\"'")) . '"',
                        explode(',', $str)
                    )) . ']';
                }
                return $str;
            },
        ];

        foreach ($strategies as $strategy) {
            $modified = $strategy($value);
            if ($modified === $value) {
                continue;
            }

            $decoded = json_decode($modified, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return is_array($decoded) ? $decoded : [(string) $decoded];
            }
        }

        return [(string) trim($value, " \t\n\r\0\x0B\"'")];
    }

    private function unescapeValue(string $value): string
    {
        $prev = $value;
        for ($iteration = 0; $iteration < 5; ++$iteration) {
            $current     = htmlspecialchars_decode($prev, ENT_QUOTES);
            $jsonDecoded = json_decode('"' . $current . '"', true);
            $current     = $jsonDecoded ?? $current;

            if ($current === $prev) {
                break;
            }

            $prev = $current;
        }

        return $prev;
    }

    private function addIndex(string $table, string $index, array $columns): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index) || $this->indexColumnsExists($table, $columns)) {
            return;
        }

        $fullTable = $this->table($table);
        $columns   = implode('`, `', $columns);
        DB::statement("ALTER TABLE `{$fullTable}` ADD INDEX `{$index}` (`{$columns}`)");
    }

    private function addCustomerJsonArrayIndexes(): void
    {
        if (! $this->supportsJsonArrayIndex()) {
            return;
        }

        $indexes = [
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

        foreach ($indexes as $table => $columns) {
            foreach ($columns as $column) {
                $this->addJsonArrayIndex($table, $column);
            }
        }
    }

    private function addJsonArrayIndex(string $table, string $column): void
    {
        if (! Schema::hasColumn($table, $column) || $this->columnType($table, $column) !== 'json') {
            return;
        }

        $index = 'idx_' . $column;
        if ($this->indexExists($table, $index) || ! $this->hasOnlyJsonScalarArrays($table, $column)) {
            return;
        }

        $fullTable = $this->table($table);
        $column    = $this->quoteIdentifier($column);
        DB::statement("ALTER TABLE `{$fullTable}` ADD INDEX `{$index}` ((CAST({$column} AS CHAR(20) ARRAY)))");
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

    private function supportsJsonArrayIndex(): bool
    {
        $version = (string) DB::selectOne('SELECT VERSION() AS version')->version;
        if (stripos($version, 'mariadb') !== false) {
            return false;
        }

        $version = preg_replace('/[^0-9.].*$/', '', $version) ?: '0.0.0';
        return version_compare($version, '8.0.17', '>=');
    }

    private function indexExists(string $table, string $index): bool
    {
        $fullTable = $this->table($table);
        $index     = DB::getPdo()->quote($index);
        return ! empty(DB::select("SHOW INDEX FROM `{$fullTable}` WHERE Key_name = {$index}"));
    }

    private function indexColumnsExists(string $table, array $columns): bool
    {
        $fullTable = $this->table($table);
        $indexes   = collect(DB::select("SHOW INDEX FROM `{$fullTable}`"))
            ->groupBy('Key_name')
            ->map(fn ($items) => collect($items)->sortBy('Seq_in_index')->pluck('Column_name')->values()->all());

        return $indexes->contains(fn (array $indexColumns) => $indexColumns === array_values($columns));
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
}
