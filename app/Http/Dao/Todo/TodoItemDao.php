<?php

declare(strict_types=1);


namespace App\Http\Dao\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Todo\TodoItem;
use crmeb\basic\BaseDao;
use Illuminate\Support\Carbon;

class TodoItemDao extends BaseDao
{
    /**
     * 待办分页查询.
     */
    public function getList(array $where = [], array $field = ['*'], int $page = 0, int $limit = 0, $sort = null, array $with = []): array
    {
        return $this->todoSearch($where)
            ->select($field)
            ->when($page && $limit, fn ($query) => $query->forPage($page, $limit))
            ->when($limit, fn ($query) => $query->limit($limit))
            ->when($sort, fn ($query) => $query->orderByRaw($this->sourceTimeExpression() . ' ' . $this->parseSortDirection($sort)))
            ->when(! $sort, fn ($query) => $query->orderByRaw($this->sourceTimeExpression() . ' DESC'))
            ->with($with)
            ->get()
            ->toArray();
    }

    public function countList(array $where = []): int
    {
        return $this->todoSearch($where)->count();
    }

    /**
     * Upsert 单条待办.
     */
    public function upsertItem(int $userId, string $type, int $sourceId, string $title, ?array $extra = null, ?string $sourceCreatedAt = null): TodoItem
    {
        return TodoItem::updateOrCreate(
            ['user_id' => $userId, 'type' => $type, 'source_id' => $sourceId],
            [
                'title'             => $title,
                'extra'             => $extra,
                'source_created_at' => $sourceCreatedAt,
                'status'            => TodoItem::STATUS_PENDING,
            ]
        );
    }

    /**
     * 标记待办完成.
     */
    public function markDone(int $userId, string $type, int $sourceId): int
    {
        return TodoItem::where('user_id', $userId)
            ->where('type', $type)
            ->where('source_id', $sourceId)
            ->where('status', TodoItem::STATUS_PENDING)
            ->update(['status' => TodoItem::STATUS_DONE]);
    }

    /**
     * 按业务源直接标记完成，用于删除等已不需要逐用户判断的场景.
     */
    public function markDoneBySource(string $type, int $sourceId): int
    {
        return TodoItem::where('type', $type)
            ->where('source_id', $sourceId)
            ->where('status', TodoItem::STATUS_PENDING)
            ->update(['status' => TodoItem::STATUS_DONE]);
    }

    /**
     * 标记某个原始日程下的所有待办实例完成.
     */
    public function markScheduleDone(int $userId, int $scheduleId): int
    {
        return TodoItem::where('user_id', $userId)
            ->where('type', TodoEnum::TYPE_SCHEDULE)
            ->where('status', TodoItem::STATUS_PENDING)
            ->where('extra->schedule_id', $scheduleId)
            ->update(['status' => TodoItem::STATUS_DONE]);
    }

    /**
     * 标记指定日期范围内不在待办 ID 列表中的日程实例完成.
     *
     * @param array<int, int> $activeSourceIds
     */
    public function markDoneExceptSourceIdsBySourceDate(int $userId, string $type, array $activeSourceIds, string $startTime, string $endTime): int
    {
        $query = TodoItem::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', TodoItem::STATUS_PENDING)
            ->whereRaw($this->sourceTimeExpression() . ' BETWEEN ? AND ?', [$startTime, $endTime])
            ->orderBy('id');

        if (! empty($activeSourceIds)) {
            $query->whereNotIn('source_id', $activeSourceIds);
        }

        $count = 0;
        foreach ($query->lazy(100) as $batch) {
            $ids = $batch->pluck('id')->toArray();
            $count += TodoItem::whereIn('id', $ids)->update(['status' => TodoItem::STATUS_DONE]);
        }

        return $count;
    }

    /**
     * 标记指定日期范围外的日程实例完成.
     */
    public function markDoneOutsideSourceDateRange(int $userId, string $type, string $startTime, string $endTime): int
    {
        return TodoItem::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', TodoItem::STATUS_PENDING)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereRaw($this->sourceTimeExpression() . ' IS NULL')
                    ->orWhereRaw($this->sourceTimeExpression() . ' < ?', [$startTime])
                    ->orWhereRaw($this->sourceTimeExpression() . ' > ?', [$endTime]);
            })
            ->update(['status' => TodoItem::STATUS_DONE]);
    }

    /**
     * 批量标记类型下不在此ID列表中的待办为完成.
     * 使用游标查询 + ORDER BY id 避免死锁（固定锁定顺序）.
     */
    public function markDoneExceptSourceIds(int $userId, string $type, array $activeSourceIds): int
    {
        $query = TodoItem::where('user_id', $userId)
            ->where('type', $type)
            ->where('status', TodoItem::STATUS_PENDING)
            ->orderBy('id');  // 固定顺序，避免死锁

        if (! empty($activeSourceIds)) {
            $query->whereNotIn('source_id', $activeSourceIds);
        }

        $count = 0;
        // 使用游标方式批量处理，每次只锁定少量行
        foreach ($query->lazy(100) as $batch) {
            $ids = $batch->pluck('id')->toArray();
            $count += TodoItem::whereIn('id', $ids)->update(['status' => TodoItem::STATUS_DONE]);
        }

        return $count;
    }

    protected function setModel(): string
    {
        return TodoItem::class;
    }

    private function todoSearch(array $where)
    {
        $type   = $where['type'] ?? '';
        $time   = $where['time'] ?? '';
        $status = $where['status'] ?? '';

        unset($where['type'], $where['time'], $where['status']);

        $query = $this->search($where)
            ->when($type && $type !== TodoEnum::TYPE_ALL, fn ($query) => $query->where('type', $type))
            ->when($status !== '', fn ($query) => $query->where('status', $status));

        if ($time !== '') {
            $startTime = Carbon::make($time)->startOfDay()->toDateTimeString();
            $endTime   = Carbon::make($time)->endOfDay()->toDateTimeString();
            $query->whereRaw($this->sourceTimeExpression() . ' BETWEEN ? AND ?', [$startTime, $endTime]);
        }

        return $query;
    }

    private function parseSortDirection($sort): string
    {
        if (is_array($sort)) {
            $direction = strtolower((string) (array_values($sort)[0] ?? 'desc'));
            return in_array($direction, ['asc', 'desc'], true) ? strtoupper($direction) : 'DESC';
        }

        return strtolower((string) $sort) === 'asc' ? 'ASC' : 'DESC';
    }

    private function sourceTimeExpression(): string
    {
        return "COALESCE(CASE WHEN `type` = '" . TodoEnum::TYPE_SCHEDULE . "' THEN JSON_UNQUOTE(JSON_EXTRACT(`extra`, '$.start_time')) END, `source_created_at`, `created_at`)";
    }
}
