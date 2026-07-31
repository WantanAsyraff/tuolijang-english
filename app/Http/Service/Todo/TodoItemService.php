<?php

declare(strict_types=1);


namespace App\Http\Service\Todo;

use App\Constants\ApproveEnum;
use App\Constants\AssessEnum;
use App\Constants\TodoEnum;
use App\Http\Dao\Approve\ApproveApplyDao;
use App\Http\Dao\Company\CompanyUserAssessDao;
use App\Http\Dao\Customer\ContractDao;
use App\Http\Dao\Customer\CustomerDao;
use App\Http\Dao\Customer\InvoiceDao;
use App\Http\Dao\News\NewsDao;
use App\Http\Dao\Program\ProgramTaskDao;
use App\Http\Dao\Schedule\ScheduleDao;
use App\Http\Dao\Todo\TodoItemDao;
use App\Http\Model\Admin\Admin;
use App\Http\Model\Todo\TodoItem;
use App\Http\Service\Approve\ApproveApplyService;
use App\Http\Service\Frame\FrameService;
use App\Http\Service\News\NewsVisitService;
use App\Http\Service\Schedule\ScheduleService;
use crmeb\basic\BaseService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class TodoItemService extends BaseService
{
    public function __construct(
        protected TodoItemDao $todoItemDao,
        protected CompanyUserAssessDao $assessDao,
        protected CustomerDao $customerDao,
        protected ContractDao $contractDao,
        protected InvoiceDao $invoiceDao,
        protected ProgramTaskDao $programTaskDao,
        protected NewsDao $newsDao,
        protected ScheduleDao $scheduleDao,
        protected ApproveApplyDao $applyDao,
    ) {}

    /**
     * 待办列表.
     */
    public function getList(array $where, array $field = ['*'], $sort = null, array $with = []): array
    {
        [$page, $limit] = $this->getPageValue();
        $list           = $this->todoItemDao->getList($where, $field, $page, $limit, $sort, $with);
        $count          = $this->todoItemDao->countList($where);

        foreach ($list as &$item) {
            $item['type_label'] = TodoEnum::TYPE_LABELS[$item['type']] ?? '';
            if (($item['type'] ?? '') === TodoEnum::TYPE_SCHEDULE && ! empty($item['extra']['schedule_id'])) {
                $item['todo_source_id'] = $item['source_id'];
                $item['source_id']      = (int) $item['extra']['schedule_id'];
            }
        }
        unset($item);

        return ['list' => $list, 'count' => $count];
    }

    /**
     * 获取待办概览（各类型数量统计）.
     * @return array<string, array{count: int, label: string}>
     */
    public function getOverview(int $userId): array
    {
        $result = [];
        foreach (TodoEnum::ALL_TYPES as $type) {
            $result[$type] = [
                'count' => $this->todoItemDao->count(['user_id' => $userId, 'type' => $type, 'status' => TodoItem::STATUS_PENDING]),
                'label' => TodoEnum::TYPE_LABELS[$type] ?? '',
            ];
        }
        return $result;
    }

    /**
     * 获取指定日期的未完成待办数量.
     */
    public function countPendingByDate(int $userId): int
    {
        return $this->todoItemDao->count([
            'user_id' => $userId,
            'status'  => TodoItem::STATUS_PENDING,
        ]);
    }

    /**
     * 同步某用户全部类型的待办.
     */
    public function syncAllForUser(int $userId): void
    {
        foreach (TodoEnum::ALL_TYPES as $type) {
            try {
                $this->syncForUser($userId, $type);
            } catch (\Throwable $e) {
                Log::error("TodoItem sync failed: userId={$userId}, type={$type}", [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);
            }
        }
    }

    /**
     * 同步指定类型集合的全部用户待办.
     *
     * @param array<int, string> $types
     */
    public function syncByTypesForAllUsers(array $types): void
    {
        Admin::where('status', 1)
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(200, function ($admins) use ($types) {
                foreach ($admins as $admin) {
                    try {
                        $this->syncByTypesForUser((int) $admin->id, $types);
                    } catch (\Throwable $e) {
                        Log::error('TodoItem syncByTypesForAllUsers failed', [
                            'userId'  => (int) $admin->id,
                            'types'   => $types,
                            'message' => $e->getMessage(),
                            'file'    => $e->getFile(),
                            'line'    => $e->getLine(),
                        ]);
                    }
                }
            });
    }

    /**
     * 同步某用户指定类型集合的待办.
     *
     * @param array<int, string> $types
     */
    public function syncByTypesForUser(int $userId, array $types): void
    {
        foreach (array_values(array_unique($types)) as $type) {
            try {
                $this->syncForUser($userId, $type);
            } catch (\Throwable $e) {
                Log::error("TodoItem sync failed: userId={$userId}, type={$type}", [
                    'message' => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                ]);
            }
        }
    }

    /**
     * 同步某用户某类型的待办到 todo_items.
     */
    public function syncForUser(int $userId, string $type): void
    {
        if ($type === TodoEnum::TYPE_SCHEDULE) {
            $maxRetries = 3;
            $retryCount = 0;

            while (true) {
                try {
                    $this->syncScheduleForUser($userId);
                    return;
                } catch (\Throwable $e) {
                    if ($this->isDeadlockException($e) && $retryCount < $maxRetries) {
                        ++$retryCount;
                        $delay = (int) (100 * pow(2, $retryCount - 1));
                        Log::warning("TodoItem schedule sync deadlock detected, retrying ({$retryCount}/{$maxRetries}) after {$delay}ms: userId={$userId}");
                        usleep($delay * 1000);
                        continue;
                    }
                    throw $e;
                }
            }
        }

        $maxRetries = 3;
        $retryCount = 0;

        while (true) {
            try {
                $this->doSyncForUser($userId, $type);
                return;
            } catch (\Throwable $e) {
                if ($this->isDeadlockException($e) && $retryCount < $maxRetries) {
                    ++$retryCount;
                    $delay = (int) (100 * pow(2, $retryCount - 1)); // 指数退避: 100, 200, 400ms
                    Log::warning("TodoItem sync deadlock detected, retrying ({$retryCount}/{$maxRetries}) after {$delay}ms: userId={$userId}, type={$type}");
                    usleep($delay * 1000);
                    continue;
                }
                throw $e;
            }
        }
    }

    /**
     * 标记某条待办完成.
     */
    public function markDone(int $userId, string $type, int $sourceId): void
    {
        $this->todoItemDao->markDone($userId, $type, $sourceId);
    }

    /**
     * 根据业务数据变更同步待办状态.
     * 由模型观察者触发，仅需传入类型和资源ID，内部查询应同步的用户范围.
     *
     * @param string $type 待办类型
     * @param int $sourceId 业务记录ID
     * @param string $action 动作类型 (create/update/delete)
     * @param array<int, int> $preloadedUserIds 预加载的用户ID列表（删除时模型已不存在）
     */
    public function syncBySourceId(string $type, int $sourceId, string $action = 'update', array $preloadedUserIds = []): void
    {
        match ($action) {
            'create' => $this->createBySourceId($type, $sourceId, $preloadedUserIds),
            'delete' => $this->deleteBySourceId($type, $sourceId, $preloadedUserIds),
            default  => $this->updateBySourceId($type, $sourceId, $preloadedUserIds),
        };
    }

    /**
     * 根据业务数据创建待办.
     *
     * @param array<int, int> $preloadedUserIds
     */
    public function createBySourceId(string $type, int $sourceId, array $preloadedUserIds = []): void
    {
        $this->syncSingleSourceForUsers($type, $sourceId, $preloadedUserIds);
    }

    /**
     * 根据业务数据更新待办.
     *
     * @param array<int, int> $preloadedUserIds
     */
    public function updateBySourceId(string $type, int $sourceId, array $preloadedUserIds = []): void
    {
        $this->syncSingleSourceForUsers($type, $sourceId, $preloadedUserIds);
    }

    /**
     * 根据业务数据删除待办.
     *
     * @param array<int, int> $preloadedUserIds
     */
    public function deleteBySourceId(string $type, int $sourceId, array $preloadedUserIds = []): void
    {
        if ($type !== TodoEnum::TYPE_SCHEDULE) {
            $this->todoItemDao->markDoneBySource($type, $sourceId);
        }

        $userIds = $this->resolveAffectedUserIds($type, $sourceId, $preloadedUserIds);
        foreach ($userIds as $userId) {
            try {
                $this->markSourceDoneForUser((int) $userId, $type, $sourceId);
            } catch (\Throwable $e) {
                Log::error('TodoItem deleteBySourceId failed', [
                    'userId'   => $userId,
                    'type'     => $type,
                    'sourceId' => $sourceId,
                    'message'  => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * 只同步当前业务源记录，不再重扫该用户该类型的全部待办.
     *
     * @param array<int, int> $preloadedUserIds
     */
    protected function syncSingleSourceForUsers(string $type, int $sourceId, array $preloadedUserIds = []): void
    {
        $userIds = $this->resolveAffectedUserIds($type, $sourceId, $preloadedUserIds);
        foreach ($userIds as $userId) {
            try {
                $this->syncSingleSourceForUser((int) $userId, $type, $sourceId);
            } catch (\Throwable $e) {
                Log::error('TodoItem syncSingleSourceForUser failed', [
                    'userId'   => $userId,
                    'type'     => $type,
                    'sourceId' => $sourceId,
                    'message'  => $e->getMessage(),
                    'file'     => $e->getFile(),
                    'line'     => $e->getLine(),
                ]);
            }
        }
    }

    /**
     * @param array<int, int> $preloadedUserIds
     * @return array<int, int>
     */
    protected function resolveAffectedUserIds(string $type, int $sourceId, array $preloadedUserIds = []): array
    {
        return array_values(array_unique(array_filter(array_map('intval', array_merge(
            $this->getUserIdsBySource($type, $sourceId),
            $preloadedUserIds
        )))));
    }

    protected function syncSingleSourceForUser(int $userId, string $type, int $sourceId): void
    {
        $items = $this->getSingleSourceTodoItems($userId, $type, $sourceId);
        if (! $items) {
            $this->markSourceDoneForUser($userId, $type, $sourceId);
            return;
        }

        if ($type === TodoEnum::TYPE_SCHEDULE) {
            $this->todoItemDao->markScheduleDone($userId, $sourceId);
        }

        foreach ($items as $item) {
            $this->todoItemDao->upsertItem(
                $userId,
                $type,
                (int) $item['id'],
                (string) $item['title'],
                $item['extra'] ?? null,
                $item['created_at'] ?? null
            );
        }
    }

    /**
     * 精准同步某个日程实例的待办状态，避免状态更新时重算未来 30 天日程.
     */
    public function syncScheduleInstanceForUser(
        int $userId,
        int $scheduleId,
        string $title,
        string $startTime,
        string $endTime,
        int $scheduleStatus,
        int $allDay = 0
    ): void {
        $startTime = Carbon::parse($startTime, config('app.timezone'))->toDateTimeString();
        $endTime   = Carbon::parse($endTime, config('app.timezone'))->toDateTimeString();
        $sourceId = $this->buildScheduleTodoSourceId($scheduleId, $startTime, $endTime);

        if (in_array($scheduleStatus, [2, 3], true) || ! $this->isInScheduleTodoWindow($startTime, $endTime)) {
            $this->todoItemDao->markDone($userId, TodoEnum::TYPE_SCHEDULE, $sourceId);
            return;
        }

        $this->todoItemDao->upsertItem(
            $userId,
            TodoEnum::TYPE_SCHEDULE,
            $sourceId,
            $title,
            [
                'schedule_id' => $scheduleId,
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'all_day'     => $allDay,
            ],
            $startTime
        );
    }

    /**
     * @return array<int, array{id: int, title: string, extra: null|array, created_at: null|string}>
     */
    protected function getSingleSourceTodoItems(int $userId, string $type, int $sourceId): array
    {
        if ($type === TodoEnum::TYPE_SCHEDULE) {
            return $this->fetchScheduleSourceItems($userId, $sourceId);
        }

        return array_values(array_filter(
            $this->fetchSourceItems($userId, $type),
            fn (array $item) => (int) $item['id'] === $sourceId
        ));
    }

    protected function markSourceDoneForUser(int $userId, string $type, int $sourceId): void
    {
        if ($type === TodoEnum::TYPE_SCHEDULE) {
            $this->todoItemDao->markScheduleDone($userId, $sourceId);
            return;
        }

        $this->todoItemDao->markDone($userId, $type, $sourceId);
    }

    /**
     * 获取某个日程在未来待办窗口内展开出的待办实例.
     *
     * @return array<int, array{id: int, title: string, extra: array<string, mixed>, created_at: null|string}>
     */
    protected function fetchScheduleSourceItems(int $userId, int $scheduleId): array
    {
        $startDate       = now()->startOfDay();
        $endDate         = now()->addDays(30)->endOfDay();
        $scheduleService = app(ScheduleService::class);
        $items           = [];

        $dayItems = $scheduleService->scheduleDateListByScheduleId(
            $userId,
            $scheduleId,
            $startDate->toDateTimeString(),
            $endDate->toDateTimeString()
        );

        foreach ($this->buildScheduleItemsForDay($dayItems) as $item) {
            if ((int) ($item['extra']['schedule_id'] ?? 0) === $scheduleId) {
                $items[(int) $item['id']] = $item;
            }
        }

        return array_values($items);
    }

    public function fetchNoticeItems(int $userId): array
    {
        $entId = (int) Admin::whereKey($userId)->value('entid');

        return $this->newsDao->select([
            'status'    => 1,
            'is_push'   => 1,
            'not_visit' => $userId,
            'entid'     => $entId,
        ], cursor: true)->map(fn ($item) => [
            'id'         => $item->id,
            'title'      => $item->title,
            'extra'      => ['push_time' => $item->push_time, 'is_read' => 0],
            'created_at' => $item->created_at?->toDateTimeString(),
        ])?->toArray();
    }

    /**
     * 根据业务数据获取应同步的用户ID列表.
     *
     * @return array<int, int>
     */
    protected function getUserIdsBySource(string $type, int $sourceId): array
    {
        return match ($type) {
            TodoEnum::TYPE_SCHEDULE => $this->getScheduleUserIds($sourceId),
            TodoEnum::TYPE_ASSESS_SELF, TodoEnum::TYPE_ASSESS_CHECK, TodoEnum::TYPE_ASSESS_APPEAL => $this->getAssessUserIds($sourceId),
            TodoEnum::TYPE_CUSTOMER => $this->getCustomerUserIds($sourceId),
            TodoEnum::TYPE_CONTRACT => $this->getContractUserIds($sourceId),
            TodoEnum::TYPE_INVOICE  => $this->getInvoiceUserIds($sourceId),
            TodoEnum::TYPE_TASK     => $this->getTaskUserIds($sourceId),
            TodoEnum::TYPE_NOTICE   => $this->getNoticeUserIds($sourceId),
            TodoEnum::TYPE_APPROVE_SUBMIT, TodoEnum::TYPE_APPROVE_PENDING => $this->getApproveUserIds($sourceId),
            default => [],
        };
    }

    protected function getScheduleUserIds(int $sourceId): array
    {
        $schedule = app(ScheduleService::class)->dao->get($sourceId);
        if (! $schedule) {
            return [];
        }
        // 日程同步给所有参与者
        return $schedule->schedule_user()->pluck('uid')->toArray();
    }

    protected function getAssessUserIds(int $sourceId): array
    {
        $assess = $this->assessDao->get($sourceId);
        if (! $assess) {
            return [];
        }
        $userIds = array_filter([
            $assess->test_uid,
            $assess->check_uid,
        ]);
        // 绩效申诉还需要上级
        if ($assess->status === AssessEnum::ASSESS_APPEAL) {
            $verifyUid = app()->get(FrameService::class)->getLevelSuper($assess->test_uid, 2);
            if ($verifyUid) {
                $userIds[] = $verifyUid;
            }
        }
        return array_values($userIds);
    }

    protected function getCustomerUserIds(int $sourceId): array
    {
        $customer = $this->customerDao->get($sourceId);
        if (! $customer) {
            return [];
        }
        return array_filter([$customer->uid]);
    }

    protected function getContractUserIds(int $sourceId): array
    {
        $contract = $this->contractDao->get($sourceId);
        if (! $contract) {
            return [];
        }
        return array_filter([$contract->uid]);
    }

    protected function getInvoiceUserIds(int $sourceId): array
    {
        $invoice = $this->invoiceDao->get($sourceId);
        if (! $invoice) {
            return [];
        }
        return array_filter([$invoice->uid]);
    }

    protected function getTaskUserIds(int $sourceId): array
    {
        $task = $this->programTaskDao->get($sourceId);
        if (! $task) {
            return [];
        }
        return array_filter([$task->uid]);
    }

    protected function getNoticeUserIds(int $sourceId): array
    {
        $news = $this->newsDao->get($sourceId);
        if (! $news) {
            return [];
        }

        if ((int) $news->status !== 1 || datetime_timestamp((string) $news->push_time) > time()) {
            return [];
        }

        return $this->getUnreadNoticeUserIds(
            $sourceId,
            (int) $news->entid
        );
    }

    /**
     * 企业动态是面向企业有效用户的未读待办，创建时不能从阅读记录反推目标用户.
     *
     * @return array<int, int>
     */
    public function getUnreadNoticeUserIds(int $sourceId, int $entId): array
    {
        $visitService = app()->get(NewsVisitService::class);
        $readUserIds  = $visitService->dao->column(['notice_id' => $sourceId], 'user_id');

        return Admin::where('status', 1)
            ->when($entId > 0, fn ($query) => $query->where('entid', $entId))
            ->when(! empty($readUserIds), fn ($query) => $query->whereNotIn('id', $readUserIds))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    protected function getApproveUserIds(int $sourceId): array
    {
        $apply = app(ApproveApplyService::class)->dao->get($sourceId);
        if (! $apply) {
            return [];
        }
        $userIds = [$apply->user_id];
        // 待审批还需要同步给审批人
        if ($apply->status === ApproveEnum::APPROVE_PENDING) {
            $verifierIds = $apply->approve_users()->pluck('user_id')->toArray();
            $userIds     = array_merge($userIds, $verifierIds);
        }
        return array_unique(array_filter($userIds));
    }

    /**
     * 判断是否为 MySQL 死锁异常.
     */
    protected function isDeadlockException(\Throwable $e): bool
    {
        $message = $e->getMessage();
        return str_contains($message, 'Deadlock found')
               || str_contains($message, '1213 Deadlock');
    }

    /**
     * 执行同步逻辑（内部方法，由 syncForUser 调用）.
     */
    protected function doSyncForUser(int $userId, string $type): void
    {
        $items = $this->fetchSourceItems($userId, $type);

        $activeSourceIds = [];
        foreach ($items as $item) {
            $this->todoItemDao->upsertItem(
                $userId,
                $type,
                $item['id'],
                $item['title'],
                $item['extra'] ?? null,
                $item['created_at'] ?? null
            );
            $activeSourceIds[] = $item['id'];
        }

        $this->todoItemDao->markDoneExceptSourceIds($userId, $type, $activeSourceIds);
    }

    /**
     * 日程待办按天分批同步，避免一次展开过大的日期范围导致内存占用过高.
     */
    protected function syncScheduleForUser(int $userId): void
    {
        $startDate = now()->startOfDay();
        $endDate   = now()->addDays(30)->startOfDay();
        $window    = clone $startDate;
        $scheduleService = app(ScheduleService::class);
        $typeIds         = $scheduleService->typeList($userId, 'id');

        while ($window->lessThanOrEqualTo($endDate)) {
            $dayStart = $window->copy()->startOfDay()->toDateTimeString();
            $dayEnd   = $window->copy()->endOfDay()->toDateTimeString();
            $dayItems = $scheduleService->scheduleDateList($userId, 1, $dayStart, $dayEnd, $typeIds);

            $items = $this->buildScheduleItemsForDay($dayItems);
            $activeSourceIds = [];
            foreach ($items as $item) {
                $this->todoItemDao->upsertItem(
                    $userId,
                    TodoEnum::TYPE_SCHEDULE,
                    $item['id'],
                    $item['title'],
                    $item['extra'] ?? null,
                    $item['created_at'] ?? null
                );
                $activeSourceIds[] = $item['id'];
            }

            $this->todoItemDao->markDoneExceptSourceIdsBySourceDate(
                $userId,
                TodoEnum::TYPE_SCHEDULE,
                $activeSourceIds,
                $dayStart,
                $dayEnd
            );

            unset($items, $activeSourceIds, $dayItems);
            gc_collect_cycles();

            $window->addDay();
        }

        $this->todoItemDao->markDoneOutsideSourceDateRange(
            $userId,
            TodoEnum::TYPE_SCHEDULE,
            $startDate->startOfDay()->toDateTimeString(),
            $endDate->endOfDay()->toDateTimeString()
        );
    }

    /**
     * 将某一天展开出来的日程记录规范化为待办写入结构.
     * @param array<int, array<string, mixed>> $dayItems
     * @return array<int, array{id: int, title: string, extra: array<string, mixed>, created_at: ?string}>
     */
    protected function buildScheduleItemsForDay(array $dayItems): array
    {
        $items = [];

        foreach ($dayItems as $dayItem) {
            foreach (($dayItem['list'] ?? []) as $item) {
                if (in_array((int) ($item['finish'] ?? -1), [2, 3], true)) {
                    continue;
                }

                $scheduleId = (int) ($item['id'] ?? 0);
                $startTime  = (string) ($item['start_time'] ?? '');
                $endTime    = (string) ($item['end_time'] ?? '');
                if (! $scheduleId || $startTime === '' || $endTime === '') {
                    continue;
                }

                $sourceId = $this->buildScheduleTodoSourceId($scheduleId, $startTime, $endTime);
                $items[$sourceId] = [
                    'id'         => $sourceId,
                    'title'      => (string) ($item['title'] ?? ''),
                    'extra'      => [
                        'schedule_id' => $scheduleId,
                        'start_time'  => $startTime,
                        'end_time'    => $endTime,
                        'all_day'     => $item['all_day'] ?? 0,
                    ],
                    'created_at' => $startTime,
                ];
            }
        }

        usort($items, fn ($a, $b) => strcmp((string) $a['extra']['start_time'], (string) $b['extra']['start_time']));
        return array_values($items);
    }

    protected function buildScheduleTodoSourceId(int $scheduleId, string $startTime, string $endTime): int
    {
        return (int) sprintf('%u', crc32($scheduleId . '|' . $startTime . '|' . $endTime));
    }

    protected function isInScheduleTodoWindow(string $startTime, string $endTime): bool
    {
        $start = Carbon::parse($startTime);
        $end   = Carbon::parse($endTime);

        return $end->greaterThanOrEqualTo(now()->startOfDay())
               && $start->lessThanOrEqualTo(now()->addDays(30)->endOfDay());
    }

    /**
     * 从源表获取待办数据并统一格式.
     * @return array<int, array{id: int, title: string, extra: null|array, created_at: null|string}>
     */
    protected function fetchSourceItems(int $userId, string $type): array
    {
        return match ($type) {
            TodoEnum::TYPE_SCHEDULE        => $this->fetchScheduleItems($userId),
            TodoEnum::TYPE_ASSESS_SELF     => $this->fetchAssessSelfItems($userId),
            TodoEnum::TYPE_ASSESS_CHECK    => $this->fetchAssessCheckItems($userId),
            TodoEnum::TYPE_ASSESS_APPEAL   => $this->fetchAssessAppealItems($userId),
            TodoEnum::TYPE_CUSTOMER        => $this->fetchCustomerItems($userId),
            TodoEnum::TYPE_CONTRACT        => $this->fetchContractItems($userId),
            TodoEnum::TYPE_INVOICE         => $this->fetchInvoiceItems($userId),
            TodoEnum::TYPE_TASK            => $this->fetchTaskItems($userId),
            TodoEnum::TYPE_NOTICE          => $this->fetchNoticeItems($userId),
            TodoEnum::TYPE_APPROVE_SUBMIT  => $this->fetchApproveSubmitItems($userId),
            TodoEnum::TYPE_APPROVE_PENDING => $this->fetchApprovePendingItems($userId),
            default                        => [],
        };
    }

    protected function fetchScheduleItems(int $userId): array
    {
        // 复用日程展开逻辑，按当前用户的 schedule_task 状态过滤已完成/已拒绝实例。
        $start           = now()->startOfDay()->toDateTimeString();
        $end             = now()->addDays(30)->endOfDay()->toDateTimeString();
        $scheduleService = app(ScheduleService::class);
        $typeIds         = $scheduleService->typeList($userId, 'id');
        $dayItems        = $scheduleService->scheduleDateList($userId, 1, $start, $end, $typeIds);

        $items = [];
        foreach ($dayItems as $dayItem) {
            foreach (($dayItem['list'] ?? []) as $item) {
                if (in_array((int) ($item['finish'] ?? -1), [2, 3], true)) {
                    continue;
                }

                $sourceId = (int) $item['id'];
                if (isset($items[$sourceId])
                    && datetime_timestamp((string) $items[$sourceId]['extra']['start_time']) <= datetime_timestamp((string) $item['start_time'])) {
                    continue;
                }

                $items[$sourceId] = [
                    'id'    => $sourceId,
                    'title' => (string) ($item['title'] ?? ''),
                    'extra' => [
                        'start_time' => $item['start_time'] ?? '',
                        'end_time'   => $item['end_time'] ?? '',
                        'all_day'    => $item['all_day'] ?? 0,
                    ],
                    'created_at' => null,
                ];
            }
        }

        if ($items) {
            $createdAtMap = $this->scheduleDao->getModel(false)
                ->whereIn('id', array_keys($items))
                ->pluck('created_at', 'id')
                ->all();
            foreach ($items as $sourceId => &$item) {
                $item['created_at'] = isset($createdAtMap[$sourceId])
                    ? (string) $createdAtMap[$sourceId]
                    : ($item['extra']['start_time'] ?: null);
            }
            unset($item);
        }

        usort($items, fn ($a, $b) => strcmp((string) $a['extra']['start_time'], (string) $b['extra']['start_time']));
        return array_slice(array_values($items), 0, 500);
    }

    protected function fetchAssessSelfItems(int $userId): array
    {
        return $this->assessDao->select([
            'test_uid' => $userId,
            'status'   => AssessEnum::ASSESS_SELF_APPRAISAL,
            'is_show'  => 1,
        ], ['id', 'name', 'period', 'end_time', 'created_at'], cursor: true)
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->name,
                'extra'      => ['period' => $item->period, 'end_time' => $item->end_time],
                'created_at' => $item->created_at?->toDateTimeString(),
            ])->all();
    }

    protected function fetchAssessCheckItems(int $userId): array
    {
        return $this->assessDao->select([
            'check_uid' => $userId,
            'status'    => AssessEnum::ASSESS_EVALUATION,
            'is_show'   => 1,
        ], ['id', 'name', 'period', 'end_time', 'created_at'], cursor: true)
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->name,
                'extra'      => ['period' => $item->period, 'end_time' => $item->end_time],
                'created_at' => $item->created_at?->toDateTimeString(),
            ])->all();
    }

    protected function fetchAssessAppealItems(int $userId): array
    {
        $verifyUid = app()->get(FrameService::class)->getLevelSuper($userId, 2);
        if (! $verifyUid) {
            return [];
        }
        $list = $this->assessDao->search([
            'check_uid' => $verifyUid,
            'status'    => AssessEnum::ASSESS_APPEAL,
            'is_show'   => 1,
        ])
            ->select(['id', 'name', 'period', 'end_time', 'created_at'])
            ->limit(500)
            ->get();

        return $list->map(fn ($item) => [
            'id'         => $item->id,
            'title'      => $item->name,
            'extra'      => ['period' => $item->period, 'end_time' => $item->end_time],
            'created_at' => $item->created_at?->toDateTimeString(),
        ])->all();
    }

    protected function fetchCustomerItems(int $userId): array
    {
        $urgentCustomerIds = $this->customerDao->getUrgentFollowUpIds(['uid' => $userId]);
        return $this->customerDao->select(['id' => $urgentCustomerIds], ['id', 'customer_name', 'uid', 'created_at'], cursor: true)
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->customer_name,
                'extra'      => null,
                'created_at' => $item->created_at?->toDateTimeString(),
            ])->all();
    }

    protected function fetchContractItems(int $userId): array
    {
        return $this->contractDao->select(['uid' => $userId, 'status' => 2], ['id', 'doc_no', 'doc_name', 'created_at'], cursor: true)
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->doc_name,
                'extra'      => ['doc_no' => $item->doc_no],
                'created_at' => $item->created_at?->toDateTimeString(),
            ])->all();
    }

    protected function fetchInvoiceItems(int $userId): array
    {
        return $this->invoiceDao->select(['uid' => $userId, 'status' => 0], ['id', 'title', 'amount', 'num', 'created_at'], cursor: true)
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->title,
                'extra'      => ['amount' => $item->amount, 'num' => $item->num],
                'created_at' => $item->created_at?->toDateTimeString(),
            ])->all();
    }

    protected function fetchTaskItems(int $userId): array
    {
        return $this->programTaskDao->select(['uid' => $userId, 'status' => 0], ['id', 'name', 'program_id', 'status', 'priority', 'plan_end', 'created_at'], cursor: true)
            ->map(fn ($item) => [
                'id'         => $item->id,
                'title'      => $item->name,
                'extra'      => ['program_id' => $item->program_id, 'status' => $item->status, 'priority' => $item->priority, 'plan_end' => $item->plan_end],
                'created_at' => $item->created_at?->toDateTimeString(),
            ])->all();
    }

    protected function fetchApproveSubmitItems(int $userId): array
    {
        return $this->applyDao->select(
            ['user_id' => $userId, 'status' => ApproveEnum::APPROVE_PENDING],
            ['id', 'approve_id', 'crud_id', 'user_id', 'status', 'created_at'],
            ['approve' => fn ($q) => $q->select(['id', 'name']), 'crudApprove' => fn ($q) => $q->select(['id', 'name']), 'card'],
            cursor: true
        )->map(function ($item) {
            $userName = $item->card?->name ?? '';
            $approveName = $item->approve_config?->name ?? '';
            $title = $approveName ? "{$userName}的{$approveName}审批" : $userName;
            return [
                'id'         => $item->id,
                'title'      => $title,
                'extra'      => ['approve_id' => $item->approve_id, 'crud_id' => $item->crud_id],
                'created_at' => $item->created_at?->toDateTimeString(),
            ];
        })->all();
    }

    protected function fetchApprovePendingItems(int $userId): array
    {
        $where = app(ApproveApplyService::class)->getWhere(['types' => 1, 'name' => '', 'verify_status' => 1], $userId);
        return $this->applyDao->select(
            $where,
            ['id', 'approve_id', 'crud_id', 'user_id', 'status', 'created_at'],
            ['approve' => fn ($q) => $q->select(['id', 'name']), 'crudApprove' => fn ($q) => $q->select(['id', 'name']), 'card'],
            cursor: true
        )->map(function ($item) {
            $userName = $item->card?->name ?? '';
            $approveName = $item->approve_config?->name ?? '';
            $title = $approveName ? "{$userName}的{$approveName}审批" : $userName;
            return [
                'id'         => $item->id,
                'title'      => $title,
                'extra'      => ['approve_id' => $item->approve_id, 'crud_id' => $item->crud_id],
                'created_at' => $item->created_at?->toDateTimeString(),
            ];
        })->all();
    }
}
