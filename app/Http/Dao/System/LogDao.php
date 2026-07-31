<?php

declare(strict_types=1);


namespace App\Http\Dao\System;

use App\Http\Model\Admin\Admin;
use App\Http\Model\System\Log;
use crmeb\basic\BaseDao;
use crmeb\basic\BaseModel;
use crmeb\traits\dao\JoinSearchTrait;
use crmeb\traits\dao\ListSearchTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class LogDao.
 */
class LogDao extends BaseDao
{
    use ListSearchTrait;
    use JoinSearchTrait;

    public $table = 'enterprise_log';

    protected int $maxCount = 1000000;

    protected array $logIndexCache = [];

    protected array $logIdRangeCache = [];

    /**
     * 设置模型.
     * @return BaseModel
     * @throws BindingResolutionException
     */
    public function getModel(bool $need = true)
    {
        $this->setMaxCount();
        $model          = parent::getModel();
        if ($need) {
            $model->setTable($this->getTableName());
            return $this->getJoinModel('uid', 'uid', model: $model);
        }
        return $model->setTable($this->getTableName());
    }

    /**
     * 查询列表.
     * @throws BindingResolutionException
     */
    public function getLogList(array $where, int $page = 0, int $limit = 0, array $with = []): array
    {
        [$count, $list] = $this->getMergedPageList($where, $page, $limit, $this->getLogSelectFields());
        if ($list) {
            $list = Log::hydrate($list)->toArray();
        }
        return compact('list', 'count');
    }

    /**
     * 清理超过保留时间的日志.
     */
    public function deleteExpiredLogs(string $expiredAt, int $batchSize = 5000): int
    {
        $deleted = 0;
        foreach ($this->getLogTableNames() as $tableName) {
            do {
                $ids = DB::table($tableName)
                    ->where('created_at', '<', $expiredAt)
                    ->orderBy('id')
                    ->limit($batchSize)
                    ->pluck('id')
                    ->all();

                if (! $ids) {
                    break;
                }

                $deleted += DB::table($tableName)->whereIn('id', $ids)->delete();
            } while (count($ids) === $batchSize);
        }

        $this->refreshCurrentLogTableCount();

        return $deleted;
    }

    /**
     * @throws BindingResolutionException
     */
    public function searchInfo($where, $page, $limit): array
    {
        if (! $limit) {
            $count = 0;
            foreach ($this->getLogTableNames() as $tableName) {
                $count += (clone $this->buildLogTableQuery($tableName, $where))->count();
            }
            return [$count, []];
        }
        return $this->getMergedPageList($where, $page, $limit, ['id', 'created_at']);
    }

    /**
     * 构建所有日志分表的联合查询.
     */
    protected function buildLogUnionQuery(array $where, bool $onlyId = true)
    {
        $queries = [];
        foreach ($this->getLogTableNames() as $tableName) {
            $query     = $this->buildLogTableQuery($tableName, $where);
            $queries[] = $onlyId ? $query->select(['id', 'created_at']) : $query->select($this->getLogSelectFields());
        }
        $query = array_shift($queries);
        foreach ($queries as $item) {
            $query->unionAll($item);
        }
        return DB::query()->fromSub($query, 'l');
    }

    /**
     * 构建单张日志分表查询条件.
     */
    protected function buildLogTableQuery(string $tableName, array $where, string $forceIndex = '')
    {
        $query = DB::table($tableName);
        if ($forceIndex && $this->logIndexExists($tableName, $forceIndex)) {
            $query->forceIndex($forceIndex);
        }
        $query->when(isset($where['entid']), function ($query) use ($where) {
            $query->where('entid', $where['entid']);
        })->when(isset($where['event_name']) && $where['event_name'], function ($query) use ($where) {
            $query->where('event_name', 'like', '%' . $where['event_name'] . '%');
        })->when(isset($where['user_name']) && $where['user_name'], function ($query) use ($where) {
            $query->where('user_name', 'like', '%' . $where['user_name'] . '%');
        })->when(isset($where['path']) && $where['path'], function ($query) use ($where) {
            $query->where('path', 'like', '%' . $where['path'] . '%');
        });
        $this->scopeTime($query, $where['time'] ?? '', 'created_at');
        return $query;
    }

    /**
     * 判断日志列表排序索引是否存在.
     */
    protected function logIndexExists(string $tableName, string $indexName): bool
    {
        $cacheKey = $tableName . ':' . $indexName;
        if (isset($this->logIndexCache[$cacheKey])) {
            return $this->logIndexCache[$cacheKey];
        }
        $table = DB::getTablePrefix() . $tableName;
        $index = DB::getPdo()->quote($indexName);
        return $this->logIndexCache[$cacheKey] = ! empty(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = {$index}"));
    }

    /**
     * 分表分页读取日志，避免深分页时一次加载 page * limit 行导致内存溢出.
     */
    protected function getMergedPageList(array $where, int $page, int $limit, array $fields): array
    {
        $sortByTime = ! empty($where['time']);
        $forceIndex = $sortByTime ? 'idx_entid_created_id' : (isset($where['entid']) ? 'idx_entid_id' : '');
        $tables     = $this->getLogTableNames();

        if (! $limit) {
            return [$this->getLogTablesCount($tables, $where), []];
        }
        if (! $sortByTime && $this->canPageByTableIdRange($tables)) {
            return $this->getIdOrderedPageList($tables, $where, $page, $limit, $fields, $forceIndex);
        }
        return $this->getCursorMergedPageList($tables, $where, $page, $limit, $fields, $forceIndex, $sortByTime);
    }

    /**
     * id 在分表间按后缀递增，默认列表可按分表计数跳过深分页 offset，避免一次加载 page * limit 行.
     */
    protected function getIdOrderedPageList(array $tables, array $where, int $page, int $limit, array $fields, string $forceIndex): array
    {
        $count     = 0;
        $list      = [];
        $offset    = $limit ? max(0, (($page ?: 1) - 1) * $limit) : 0;
        $remaining = $limit;
        foreach ($tables as $tableName) {
            $tableCount = (clone $this->buildLogTableQuery($tableName, $where))->count();
            $count += $tableCount;
            if ($limit && $offset >= $tableCount) {
                $offset -= $tableCount;
                continue;
            }
            if ($limit && $remaining <= 0) {
                continue;
            }

            $query = $this->buildLogTableQuery($tableName, $where, $forceIndex)->select($fields)->orderByDesc('id');
            if ($limit) {
                $query->offset($offset)->limit($remaining);
            }
            $rows = $query->get()->map(fn ($item) => (array) $item)->toArray();
            $list = array_merge($list, $rows);
            if ($limit) {
                $remaining -= count($rows);
                $offset = 0;
            }
        }
        return [$count, $list];
    }

    /**
     * 小批量游标式多路归并，兼容历史分表 id 区间重叠和 created_at 排序场景.
     */
    protected function getCursorMergedPageList(array $tables, array $where, int $page, int $limit, array $fields, string $forceIndex, bool $sortByTime): array
    {
        $count  = 0;
        $states = [];
        foreach ($tables as $tableName) {
            $tableCount = (clone $this->buildLogTableQuery($tableName, $where))->count();
            $count += $tableCount;
            if ($tableCount) {
                $states[] = [
                    'table'     => $tableName,
                    'cursor'    => null,
                    'position'  => 0,
                    'buffer'    => [],
                    'exhausted' => false,
                ];
            }
        }
        if (! $limit || ! $states) {
            return [$count, []];
        }

        $list      = [];
        $seen      = 0;
        $offset    = max(0, (($page ?: 1) - 1) * $limit);
        $chunkSize = max(100, min(1000, $limit * 10));
        while (count($list) < $limit) {
            $bestIndex = null;
            $bestRow   = null;
            foreach ($states as $index => $state) {
                if ($state['exhausted']) {
                    continue;
                }
                if ($state['position'] >= count($state['buffer'])) {
                    $rows = $this->fetchLogRows($state['table'], $where, $fields, $forceIndex, $state['cursor'], $chunkSize, $sortByTime);
                    $states[$index]['buffer']    = $rows;
                    $states[$index]['position']  = 0;
                    $states[$index]['cursor']    = $rows ? end($rows) : $state['cursor'];
                    $states[$index]['exhausted'] = ! $rows;
                    if (! $rows) {
                        continue;
                    }
                }
                $row = $states[$index]['buffer'][$states[$index]['position']];
                if ($bestRow === null || $this->compareLogRows($row, $bestRow, $sortByTime) < 0) {
                    $bestRow   = $row;
                    $bestIndex = $index;
                }
            }
            if ($bestIndex === null) {
                break;
            }
            ++$states[$bestIndex]['position'];
            if ($seen++ < $offset) {
                continue;
            }
            $list[] = $bestRow;
        }
        return [$count, $list];
    }

    protected function fetchLogRows(string $tableName, array $where, array $fields, string $forceIndex, ?array $cursor, int $limit, bool $sortByTime): array
    {
        $query = $this->buildLogTableQuery($tableName, $where, $forceIndex)->select($fields);
        if ($sortByTime) {
            if ($cursor) {
                $query->where(function ($query) use ($cursor) {
                    $query->where('created_at', '<', $cursor['created_at'])
                        ->orWhere(function ($query) use ($cursor) {
                            $query->where('created_at', $cursor['created_at'])->where('id', '<', $cursor['id']);
                        });
                });
            }
            $query->orderByDesc('created_at')->orderByDesc('id');
        } else {
            if ($cursor) {
                $query->where('id', '<', $cursor['id']);
            }
            $query->orderByDesc('id');
        }
        return $query->limit($limit)->get()->map(fn ($item) => (array) $item)->toArray();
    }

    protected function compareLogRows(array $left, array $right, bool $sortByTime): int
    {
        if (! $sortByTime) {
            return (int) $right['id'] <=> (int) $left['id'];
        }
        $timeCompare = strcmp((string) $right['created_at'], (string) $left['created_at']);
        return $timeCompare ?: (int) $right['id'] <=> (int) $left['id'];
    }

    protected function getLogTablesCount(array $tables, array $where): int
    {
        $count = 0;
        foreach ($tables as $tableName) {
            $count += (clone $this->buildLogTableQuery($tableName, $where))->count();
        }
        return $count;
    }

    protected function canPageByTableIdRange(array $tables): bool
    {
        $previousMinId = null;
        foreach ($tables as $tableName) {
            $range = $this->getLogTableIdRange($tableName);
            if ($range['min'] === null || $range['max'] === null) {
                continue;
            }
            if ($previousMinId !== null && $range['max'] > $previousMinId) {
                return false;
            }
            $previousMinId = $range['min'];
        }
        return true;
    }

    protected function getLogTableIdRange(string $tableName): array
    {
        if (isset($this->logIdRangeCache[$tableName])) {
            return $this->logIdRangeCache[$tableName];
        }
        $range = DB::table($tableName)->selectRaw('MIN(id) AS min_id, MAX(id) AS max_id')->first();
        return $this->logIdRangeCache[$tableName] = [
            'min' => isset($range->min_id) ? (int) $range->min_id : null,
            'max' => isset($range->max_id) ? (int) $range->max_id : null,
        ];
    }

    /**
     * 日志列表返回字段.
     */
    protected function getLogSelectFields(): array
    {
        return [
            'id',
            'event_name',
            'uid',
            'terminal',
            'method',
            'last_ip',
            'created_at',
            'path',
            'user_name',
        ];
    }

    /**
     * 获取当前日志分表列表.
     */
    protected function getLogTableNames(): array
    {
        $maxNum = (int) DB::table('sub_table')->where('table_name', $this->table)->value('num');
        $tables = [];
        for ($i = $maxNum; $i >= 0; --$i) {
            $tableName = $this->table . '_' . $i;
            if (Schema::hasTable($tableName)) {
                $tables[] = $tableName;
            }
        }
        foreach ($this->getExistingLogTableNames() as $tableName) {
            if (! in_array($tableName, $tables, true)) {
                $tables[] = $tableName;
            }
        }
        usort($tables, fn ($a, $b) => $this->getLogTableSuffix($b) <=> $this->getLogTableSuffix($a));
        if (! $tables) {
            $tables[] = $this->getTableName();
        }
        return $tables;
    }

    /**
     * 扫描数据库中实际存在的日志分表，避免 sub_table 指针滞后时漏查新表.
     */
    protected function getExistingLogTableNames(): array
    {
        $prefix = DB::getTablePrefix();
        $tables = [];
        try {
            $pattern = '^' . preg_quote($prefix . $this->table . '_', '/') . '[0-9]+$';
            $rows    = DB::select('SELECT TABLE_NAME AS table_name FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME REGEXP ?', [$pattern]);
        } catch (\Throwable $e) {
            $pattern = DB::getPdo()->quote($prefix . $this->table . '\_%');
            $rows    = DB::select('SHOW TABLES LIKE ' . $pattern);
        }
        foreach ($rows as $item) {
            $tableName = (string) (array_values((array) $item)[0] ?? '');
            $tableName = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $tableName);
            if (preg_match('/^' . preg_quote($this->table, '/') . '_\d+$/', $tableName)) {
                $tables[] = $tableName;
            }
        }
        return array_values(array_unique($tables));
    }

    /**
     * 获取日志分表数字后缀.
     */
    protected function getLogTableSuffix(string $tableName): int
    {
        return (int) preg_replace('/^' . preg_quote($this->table, '/') . '_/', '', $tableName);
    }

    /**
     * 刷新当前写入分表计数，避免清理后切表判断使用旧数据.
     */
    protected function refreshCurrentLogTableCount(): void
    {
        $tableName = (string) DB::table('sub_table')->where('table_name', $this->table)->value('sub_table_name');
        if ($tableName && Schema::hasTable($tableName)) {
            DB::table('sub_table')->where('table_name', $this->table)->update([
                'count' => DB::table($tableName)->count(),
            ]);
        }
    }

    /**
     * 设置单张分表最大记录数，最小为 2，防止异常配置导致无限切表.
     */
    protected function setMaxCount(): void
    {
        $this->maxCount = max(2, (int) env('DB_SUBTABLE_COUNT', 1000000));
    }

    /**
     * 分表查询使用查询构造器，手动补齐日志时间筛选.
     */
    protected function scopeTime($query, string $time, string $field)
    {
        if (! $time) {
            return;
        }
        if (str_contains($time, '-')) {
            [$startTime, $endTime] = array_pad(explode('-', $time, 2), 2, '');
            $startTime             = str_replace('/', '-', trim($startTime));
            $endTime               = str_replace('/', '-', trim($endTime));
            if ($startTime && $endTime) {
                if (! str_contains($startTime, ':') && ! str_contains($endTime, ':')) {
                    $query->whereDate($field, '>=', $startTime)->whereDate($field, '<', date('Y-m-d', datetime_timestamp($endTime) + 86400));
                    return;
                }
                $query->whereBetween($field, [$startTime, $startTime === $endTime ? date('Y-m-d H:i:s', datetime_timestamp($endTime) + 86400) : $endTime]);
                return;
            }
            if ($startTime) {
                $query->where($field, '>=', $startTime);
                return;
            }
            if ($endTime) {
                $query->where($field, '<', $endTime);
                return;
            }
        }
        match ($time) {
            'today'      => $query->whereBetween($field, [now()->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()]),
            'yesterday'  => $query->whereBetween($field, [now()->subDay()->startOfDay()->toDateTimeString(), now()->subDay()->endOfDay()->toDateTimeString()]),
            'week'       => $query->whereBetween($field, [now()->startOfWeek()->toDateTimeString(), now()->endOfWeek()->toDateTimeString()]),
            'last week'  => $query->whereBetween($field, [now()->subWeek()->startOfWeek()->toDateTimeString(), now()->subWeek()->endOfWeek()->toDateTimeString()]),
            'month'      => $query->whereBetween($field, [now()->startOfMonth()->toDateTimeString(), now()->endOfMonth()->toDateTimeString()]),
            'last month' => $query->whereBetween($field, [now()->subMonth()->startOfMonth()->toDateTimeString(), now()->subMonth()->endOfMonth()->toDateTimeString()]),
            'year'       => $query->whereBetween($field, [now()->startOfYear()->toDateTimeString(), now()->endOfYear()->toDateTimeString()]),
            'last year'  => $query->whereBetween($field, [now()->subYear()->startOfYear()->toDateTimeString(), now()->subYear()->endOfYear()->toDateTimeString()]),
            'quarter'    => $query->whereBetween($field, [now()->startOfQuarter()->toDateTimeString(), now()->endOfQuarter()->toDateTimeString()]),
            'lately7'    => $query->whereBetween($field, [now()->subDays(6)->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()]),
            'lately30'   => $query->whereBetween($field, [now()->subDays(29)->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()]),
            default      => null,
        };
    }

    /**
     * 设置模型.
     *
     * @return mixed|string
     */
    protected function setModel()
    {
        return Log::class;
    }

    protected function setModelB(): string
    {
        return Admin::class;
    }

    /**
     * 创建表.
     */
    protected function createTable(string $tableName, int $suffix = 0)
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('id');
            $table->string('uid', 36)->default('')->index()->comment('用户ID');
            $table->string('user_name', 64)->default('')->index()->comment('管理员姓名');
            $table->string('path', 128)->default('')->comment('链接');
            $table->string('method', 20)->default('')->comment('访问方式');
            $table->string('event_name', 60)->default('')->comment('行为');
            $table->integer('entid')->index()->default(0)->comment('企业ID');
            $table->string('type', 32)->default('')->index()->comment('类型');
            $table->string('terminal', 100)->default('')->index()->comment('访问终端');
            $table->ipAddress('last_ip')->comment('访问ip');
            $table->timestamps();

            $table->index(['uid', 'entid'], 'entid_uid');
            $table->index(['entid', 'id'], 'idx_entid_id');
            $table->index(['entid', 'created_at', 'id'], 'idx_entid_created_id');

            $table->engine    = 'InnoDB';
            $table->charset   = 'utf8';
            $table->collation = 'utf8_unicode_ci';
        });
        if ($suffix) {
            $query = 'ALTER TABLE ' . DB::connection()->getQueryGrammar()->wrapTable($tableName) . ' AUTO_INCREMENT=' . ($this->maxCount * $suffix);
            DB::statement($query);
        }
    }

    /**
     * 获取表名.
     * @return mixed|string
     */
    protected function getTableName()
    {
        $this->setMaxCount();
        return DB::transaction(function () {
            $res            = DB::table('sub_table')->where('table_name', $this->table)->lockForUpdate()->first(['sub_table_name', 'num', 'count']);
            $existingTables = $this->getExistingLogTableNames();
            if (! $res) {
                usort($existingTables, fn ($a, $b) => $this->getLogTableSuffix($b) <=> $this->getLogTableSuffix($a));
                $tableName = $existingTables[0] ?? $this->table . '_' . 0;
                $suffix    = $this->getLogTableSuffix($tableName);
                if (! Schema::hasTable($tableName)) {
                    $this->createTable($tableName, $suffix);
                    $count = 0;
                } else {
                    $count = DB::table($tableName)->count();
                }
                DB::table('sub_table')->insert(['table_name' => $this->table, 'sub_table_name' => $tableName, 'num' => $suffix, 'count' => $count]);
            } else {
                $tableName = $res->sub_table_name;
                $suffix    = (int) $res->num;
                $count     = (int) $res->count;
            }
            foreach ($existingTables as $existingTable) {
                $existingSuffix = $this->getLogTableSuffix($existingTable);
                if ($existingSuffix > $suffix) {
                    $tableName = $existingTable;
                    $suffix    = $existingSuffix;
                    $count     = DB::table($tableName)->count();
                    DB::table('sub_table')->where('table_name', $this->table)->update([
                        'sub_table_name' => $tableName,
                        'num'            => $suffix,
                        'count'          => $count,
                    ]);
                }
            }
            if (! Schema::hasTable($tableName)) {
                $this->createTable($tableName, $suffix);
                DB::table('sub_table')->where('table_name', $this->table)->update(['count' => 0]);
                $count = 0;
            } elseif (! $count) {
                $count = DB::table($tableName)->count();
                DB::table('sub_table')->where('table_name', $this->table)->update(['count' => $count]);
            }
            while ($count >= ($this->maxCount - 1)) {
                $tableName = $this->table . '_' . ($suffix + 1);
                if (! Schema::hasTable($tableName)) {
                    $this->createTable($tableName, $suffix + 1);
                    $count = 0;
                } else {
                    $count = DB::table($tableName)->count();
                }
                DB::table('sub_table')->where('table_name', $this->table)->update([
                    'sub_table_name' => $tableName,
                    'num'            => $suffix + 1,
                    'count'          => $count,
                ]);
                ++$suffix;
            }
            return $tableName;
        });
    }
}
