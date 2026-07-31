<?php

declare(strict_types=1);


namespace App\Http\Service\Schedule;

use App\Constants\AttachEnum;
use App\Constants\CacheEnum;
use App\Constants\CommonEnum;
use App\Constants\NoticeEnum;
use App\Constants\ScheduleEnum;
use App\Constants\TodoEnum;
use App\Events\BusinessDataChangeEvent;
use App\Events\SystemMessageEvent;
use App\Http\Contract\Schedule\ScheduleInterface;
use App\Http\Dao\Schedule\ScheduleDao;
use App\Http\Dao\Schedule\ScheduleRemindDao;
use App\Http\Dao\Schedule\ScheduleReplyDao;
use App\Http\Dao\Schedule\ScheduleTaskDao;
use App\Http\Dao\Schedule\ScheduleTypeDao;
use App\Http\Dao\Schedule\ScheduleUserDao;
use App\Http\Model\Schedule\Schedule;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\Attach\AttachService;
use App\Http\Service\Attendance\AttendanceArrangeService;
use App\Http\Service\Attendance\CalendarConfigService;
use App\Http\Service\Customer\FollowUpService;
use App\Http\Service\Customer\RemindService;
use App\Http\Service\Customer\OrderService;
use App\Http\Service\Customer\CustomerService;
use App\Http\Service\Notice\NoticeRecordService;
use App\Http\Service\Todo\TodoItemService;
use App\Task\message\StatusChangeTask;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use crmeb\basic\BaseService;
use crmeb\services\FormService as Form;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 日程相关业务.
 * @mixin ScheduleDao
 */
class ScheduleService extends BaseService implements ScheduleInterface
{
    public ScheduleRemindDao $remindDao;

    public int $cacheTtl;

    private ScheduleTypeDao $typeDao;

    private ScheduleUserDao $userDao;

    private ScheduleTaskDao $taskDao;

    private ScheduleReplyDao $replyDao;

    /**
     * @var Application|mixed|Repository
     */
    private mixed $timeZone;

    public function __construct(
        ScheduleDao $dao,
        ScheduleRemindDao $remindDao,
        ScheduleTypeDao $typeDao,
        ScheduleUserDao $userDao,
        ScheduleTaskDao $taskDao,
        ScheduleReplyDao $replyDao,
    ) {
        $this->dao       = $dao;
        $this->remindDao = $remindDao;
        $this->typeDao   = $typeDao;
        $this->userDao   = $userDao;
        $this->taskDao   = $taskDao;
        $this->replyDao  = $replyDao;
        $this->timeZone  = config('app.timezone');
        $this->cacheTtl  = (int) sys_config('system_cache_ttl', 3600);
    }

    public function userDao()
    {
        return $this->userDao;
    }

    public function remindDao()
    {
        return $this->remindDao;
    }

    /**
     * 获取日程类型列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function typeList(int $uid, array|string $field = ['*']): array
    {
        if (is_array($field)) {
            return $this->typeDao->select(['userid_like' => $uid], $field)?->toArray();
        }
        if (is_string($field)) {
            return $this->typeDao->column(['userid_like' => $uid], $field);
        }
        return [];
    }

    /**
     * 日程类型创建表单.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function typeCreateForm(): array
    {
        return $this->createElementForm('添加日程类型', $this->getTypeForm(collect()), '/ent/schedule/type/save');
    }

    /**
     * 保存日程类型.
     * @throws BindingResolutionException
     */
    public function saveType(int $uid, array $data): bool
    {
        $data['user_id']   = $uid;
        $data['entid']     = 1;
        $data['is_public'] = 1;
        if (! $data['name']) {
            throw $this->exception('请填写日程类型名称');
        }
        return (bool) $this->typeDao->create($data);
    }

    /**
     * 日程类型修改表单.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function typeEditForm(int $id, int $uid): array
    {
        $type = $this->typeDao->get(['id' => $id, 'user_id' => $uid])?->toArray();
        if (! $type) {
            throw $this->exception('修改的日程类型不存在');
        }
        return $this->createElementForm('修改日程类型', $this->getTypeForm(collect($type)), '/ent/schedule/type/update/' . $id, 'put');
    }

    /**
     * 修改日程类型.
     * @throws BindingResolutionException
     */
    public function updateType(int $id, int $uid, array $data): bool
    {
        $info = $this->typeDao->get(['id' => $id, 'user_id' => $uid])?->toArray();
        if (! $info) {
            throw $this->exception('修改的日程类型不存在');
        }
        $res = $this->transaction(function () use ($id, $data, $info) {
            if ($info['color'] != $data['color']) {
                $this->dao->update(['cid' => $id], ['color' => $data['color']]);
            }
            return (bool) $this->typeDao->update($id, $data);
        });
        return $res && Cache::tags([CacheEnum::TAG_SCHEDULE])->flush();
    }

    /**
     * 删除日程类型.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function deleteType(int $id, int $uid): bool
    {
        if (! $this->typeDao->exists(['id' => $id, 'user_id' => $uid])) {
            throw $this->exception('未找到该日程类型');
        }
        return (bool) $this->typeDao->delete($id);
    }

    /**
     * 获取日程列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function scheduleList(int $userId, int $entId, string $start, string $end, array $cid = [], int $period = 1): array
    {
        $detailKey = md5($userId . $period . json_encode($cid) . $start . $end . 'schedule');
        $data      = Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            $detailKey,
            (int) sys_config('system_cache_ttl', 3600),
            function () use ($period, $start, $end, $userId, $cid) {
                $assistUid    = toArray($this->userDao->column(['uid' => $userId], 'schedule_id'));
                $masterUid    = $this->dao->column(['uid' => $userId], 'id');
                $where['id']  = array_unique(array_merge($assistUid, $masterUid));
                $where['cid'] = $cid;
                if (in_array(ScheduleEnum::TYPE_PERSONAL, $cid)) {
                    $assistCid = $this->dao->column(['id' => $assistUid], 'cid') ?: [];
                    $userCid   = $this->typeDao->column(['user_id' => $userId], 'id') ?: [];
                    $assistCid = array_values(array_filter($assistCid, function ($item) use ($userCid) {
                        return ! in_array($item, [
                            ScheduleEnum::TYPE_PERSONAL,
                            ScheduleEnum::TYPE_CLIENT_TRACK,
                            ScheduleEnum::TYPE_CLIENT_RENEW,
                            ScheduleEnum::TYPE_CLIENT_RETURN,
                            ScheduleEnum::TYPE_REPORT_RENEW,
                        ]) && ! in_array($item, $userCid);
                    }));
                    $where['cid'] = array_unique(array_merge($cid, $assistCid));
                }
                $field = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'rate', 'days', 'link_id', 'fail_time'];
                $list  = $this->listHandler(toArray($this->dao->select($where, $field, [
                    'master' => fn ($query) => $query->select(['admin.id', 'name', 'avatar', 'phone']),
                    'type'   => fn ($query) => $query->select(['id', 'name', 'color', 'info']),
                    'user'   => fn ($query) => $query->select(['admin.id', 'name', 'avatar', 'phone']),
                    'remind' => fn ($query) => $query->select(['schedule_remind.uniqued', 'schedule_remind.sid', 'schedule_remind.remind_day', 'schedule_remind.remind_time']),
                ])));
                $data = $this->checkPeriod($period, $list, $start, $end, $userId);
                return json_encode(array_values($data), JSON_UNESCAPED_UNICODE);
            }
        );
        return json_decode($data, true);
    }

    /**
     * 保存日程.
     * @throws BindingResolutionException
     */
    public function saveSchedule(int $userId, int $entId, array $data, int $id = 0): bool
    {
        $remind  = $this->getRemindInfo($data);
        $members = $data['member'] ?? [];

        // 全天日程：结束时间设置为 23:59:59
        $startTime = $data['start_time'];
        $endTime   = $data['end_time'];
        if (! empty($data['all_day'])) {
            $endTime = Carbon::parse($data['end_time'], $this->timeZone)->endOfDay()->toDateTimeString();
        }

        $schedule = [
            'uid'        => $userId,
            'cid'        => $data['cid'],
            'color'      => $this->typeDao->value($data['cid'], 'color') ?: '',
            'title'      => $data['title'],
            'content'    => $data['content'],
            'all_day'    => $data['all_day'],
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'fail_time'  => $data['fail_time'] ?: null,
            'period'     => $remind ? $remind['period'] : 0,
            'rate'       => $remind ? $remind['rate'] : 1,
            'days'       => $remind ? $remind['days'] : null,
            'remind'     => (int) ($data['remind'] >= 0),
            'link_id'    => $data['link_id'] ?? 0,
        ];
        return $this->transaction(function () use ($remind, $entId, $members, $userId, $schedule, $id, $data) {
            if ($id) {
                Schedule::withoutEvents(fn () => $this->dao->update($id, $schedule));
                $this->remindDao->delete(['sid' => $id]);
                if ($data['remind'] >= 0) {
                    foreach ($members as $member) {
                        $remind['sid']     = $id;
                        $remind['uid']     = $member;
                        $remind['entid']   = $entId;
                        $remind['uniqued'] = $data['uniqued'] ?? '';
                        $this->remindDao->create($remind);
                    }
                }
                $oldUids = $this->userDao->column(['schedule_id' => $id, 'is_master' => 0], 'uid');
                if ($diff = array_diff($oldUids, array_map('intval', $members))) {
                    $this->userDao->delete(['uid' => $diff, 'schedule_id' => $id]);
                    $this->clearScheduleCache($diff);
                }
                if (array_diff(array_map('intval', $members), $oldUids)) {
                    foreach (array_diff(array_map('intval', $members), $oldUids) as $member) {
                        $this->userDao->create(['uid' => $member, 'schedule_id' => $id]);
                        $this->clearScheduleCache($member);
                    }
                }
                $this->clearScheduleCache($userId);
                $this->dispatchTodoSync($id, 'update', array_merge($oldUids, array_map('intval', $members)));
                return true;
            }
            $save = Schedule::withoutEvents(fn () => $this->dao->create($schedule));
            collect($members ?? [])->filter()->each(function ($member) use ($save, $entId, $data, $remind) {
                $this->userDao->create(['uid' => $member, 'schedule_id' => $save->id]);
                if ($data['remind'] >= 0) {
                    $remind['sid']     = $save->id;
                    $remind['uid']     = $member;
                    $remind['entid']   = $entId;
                    $remind['uniqued'] = $data['uniqued'] ?? '';
                    $this->remindDao->create($remind);
                }
            });
            if (in_array($data['cid'], [ScheduleEnum::TYPE_CLIENT_RENEW, ScheduleEnum::TYPE_CLIENT_RETURN])) {
                app()->get(RemindService::class)->updatePeriod(1, $data['uniqued'], [$data['start_time'], $data['end_time']]);
            }
            $this->clearScheduleCache($userId);
            $this->clearScheduleCache($members);
            $this->dispatchTodoSync((int) $save->id, 'create', array_map('intval', $members));
            return (bool) $save;
        });
    }

    protected function dispatchTodoSync(int $scheduleId, string $action, array $userIds = []): void
    {
        try {
            BusinessDataChangeEvent::dispatch(
                TodoEnum::TYPE_SCHEDULE,
                $scheduleId,
                $action,
                array_values(array_unique(array_filter($userIds)))
            );
        } catch (\Throwable) {
            // 待办同步失败不能影响日程主流程.
        }
    }

    /**
     * 修改日程.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function updateSchedule(int $entId, int $id, array $data, int $userId = 0): bool
    {
        $res  = false;
        $info = toArray($this->dao->get($id, with: ['user', 'remind']));
        switch ($data['type']) {
            case ScheduleEnum::CHANGE_AFTER:
                if (datetime_timestamp($data['start']) <= datetime_timestamp($info['start_time'])) {
                    $res = $this->saveSchedule($userId ?: $info['uid'], $entId, $data, $id);
                } elseif (datetime_timestamp($data['start']) > datetime_timestamp($info['start_time'])) {
                    switch ($info['period']) {
                        case ScheduleEnum::REPEAT_WEEK:
                        case ScheduleEnum::REPEAT_MONTH:
                        case ScheduleEnum::REPEAT_YEAR:
                        case ScheduleEnum::REPEAT_DAY:
                            $this->saveSchedule($userId, $entId, $data);
                            break;
                    }
                    $failTime = $this->getPreviousPeriod($info['end_time'], $data['end'], $info['period'], $info['rate'], $info['days']);
                    $res      = $this->updateTime($id, failTime: $failTime);
                }
                break;
            case ScheduleEnum::CHANGE_NOW:
                if (datetime_timestamp($data['start']) == datetime_timestamp($info['start_time'])) {
                    [$startTime, $endTime] = $this->getNextPeriod($data['start'], $data['end'], $info['period'], $info['rate'], $info['days']);
                    if (! $info['fail_time'] || datetime_timestamp($startTime) <= datetime_timestamp($info['fail_time'])) {
                        $this->saveSchedule($userId, $entId, [
                            'title'       => $info['title'],
                            'content'     => $info['content'],
                            'cid'         => $info['cid'],
                            'color'       => $info['color'],
                            'remind'      => $info['remind'],
                            'remind_time' => $info['remind'] ? $info['remind']['remind_day'] . ' ' . $info['remind']['remind_time'] : now()->toDateTimeString(),
                            'period'      => $info['period'],
                            'rate'        => $info['rate'],
                            'days'        => $info['days'],
                            'all_day'     => $info['all_day'],
                            'start_time'  => $startTime,
                            'end_time'    => $endTime,
                            'fail_time'   => $info['fail_time'],
                            'member'      => $info['user'] ? array_column($info['user'], 'id') : [],
                        ]);
                    }
                    $data['fail_time'] = $data['end'];
                    $res               = $this->saveSchedule($userId, $entId, $data, $id);
                } elseif (datetime_timestamp($data['start']) > datetime_timestamp($info['start_time'])) {
                    switch ($info['period']) {
                        case ScheduleEnum::REPEAT_WEEK:
                        case ScheduleEnum::REPEAT_MONTH:
                        case ScheduleEnum::REPEAT_YEAR:
                        case ScheduleEnum::REPEAT_DAY:
                            $data['fail_time'] = Carbon::parse($data['end'], $this->timeZone)->endOfDay()->toDateTimeString();
                            $this->saveSchedule($userId, $entId, $data);
                            break;
                    }
                    $failTime              = $this->getPreviousPeriod($info['end_time'], $data['end'], $info['period'], $info['rate'], $info['days']);
                    $res                   = $this->updateTime($id, failTime: $failTime);
                    [$startTime, $endTime] = $this->getNextPeriod($data['start'], $data['end'], $info['period'], $info['rate'], $info['days']);
                    if (! $info['fail_time'] || datetime_timestamp($startTime) <= datetime_timestamp($info['fail_time'])) {
                        $this->saveSchedule($userId, $entId, [
                            'title'       => $info['title'],
                            'content'     => $info['content'],
                            'cid'         => $info['cid'],
                            'color'       => $info['color'],
                            'remind'      => $info['remind'],
                            'remind_time' => $info['remind'] ? $info['remind']['remind_day'] . ' ' . $info['remind']['remind_time'] : now()->toDateTimeString(),
                            'period'      => $info['period'],
                            'rate'        => $info['rate'],
                            'days'        => $info['days'],
                            'all_day'     => $info['all_day'],
                            'start_time'  => $startTime,
                            'end_time'    => $endTime,
                            'fail_time'   => $info['fail_time'],
                            'member'      => $info['user'] ? array_column($info['user'], 'id') : [],
                        ]);
                    }
                }
                break;
            default:
                $res = $this->saveSchedule($userId, $entId, $data, $id);
        }
        $userIds = array_column($info['user'], 'id');
        array_push($userIds, $userId);
        $this->clearScheduleCache($userIds);
        return $res;
    }

    /**
     * 删除日程
     * @param int $userId
     * @param int $entId
     * @param int $id
     * @param array $data
     * @return bool
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function deleteSchedule(int $userId, int $entId, int $id, array $data): bool
    {
        $res = false;
        $info = $this->dao->get($id, with: ['scheduleUser', 'remindData']);
        if (! $info) {
            throw $this->exception('未找到相关记录');
        }
        // 提取用户ID
        $userIds = collect($info->scheduleUser ?? [])->pluck('uid')->push($userId)->unique()->all();

        switch ($data['type']) {
            case ScheduleEnum::CHANGE_AFTER:
                $start = datetime_timestamp($data['start']);
                $end = datetime_timestamp($data['end']);
                $infoStart = datetime_timestamp($info->start_time);
                $infoEnd = datetime_timestamp($info->end_time);
                if ($start <= $infoStart && $end <= $infoEnd) {
                    $res = $this->deleteScheduleAllRelated($info);
                } elseif ($start > $infoStart) {
                    $period = $info->period;
                    if (in_array($period, [ScheduleEnum::REPEAT_WEEK, ScheduleEnum::REPEAT_MONTH, ScheduleEnum::REPEAT_YEAR, ScheduleEnum::REPEAT_DAY])) {
                        $failTime = $this->getPreviousPeriod($info->end_time, $data['end'], $period, $info->rate, $info->days);
                        $res = $this->updateTime($id, failTime: $failTime);
                    } else {
                        $res = $this->deleteScheduleAllRelated($info);
                    }
                }
                $this->changeNoticeStatus($entId, $info->toArray(), $data['start'], $data['end'], true);
                break;
            case ScheduleEnum::CHANGE_NOW:
                $start = datetime_timestamp($data['start']);
                $infoStart = datetime_timestamp($info->start_time);
                if ($start === $infoStart) {
                    if (! $info->period) {
                        $res = $this->deleteScheduleAllRelated($info);
                    } else {
                        [$nextStart, $nextEnd] = $this->getNextPeriod($data['start'], $data['end'], $info->period, $info->rate, $info->days);
                        $res = $this->updateTime($id, startTime: $nextStart, endTime: $nextEnd, failTime: $info->fail_time);
                    }
                } elseif ($start > $infoStart) {
                    $failTime = $this->getPreviousPeriod($info->end_time, $data['end'], $info->period, $info->rate, $info->days);
                    $res = $this->updateTime($id, failTime: $failTime);
                    [$nextStart, $nextEnd] = $this->getNextPeriod($data['start'], $data['end'], $info->period, $info->rate, $info->days);
                    if (is_null($info->fail_time) || datetime_timestamp($nextStart) <= datetime_timestamp($info->fail_time)) {
                        $this->saveSchedule($userId, $entId, [
                            'title'       => $info->title,
                            'content'     => $info->content,
                            'cid'         => $info->cid,
                            'color'       => $info->color,
                            'remind'      => $info->remind,
                            'remind_time' => $info->remindData ? "{$info->remindData->remind_day} {$info->remindData->remind_time}" : now()->toDateTimeString(),
                            'period'      => $info->period,
                            'rate'        => $info->rate,
                            'days'        => $info->days,
                            'all_day'     => $info->all_day,
                            'start_time'  => $nextStart,
                            'end_time'    => $nextEnd,
                            'fail_time'   => $info->fail_time,
                            'uniqued'     => $info->remindData?->uniqued ?? '',
                            'link_id'     => $info->link_id,
                            'member'      => collect($info->scheduleUser)->pluck('uid')->all(),
                        ]);
                    }
                }
                $this->changeNoticeStatus($entId, $info->toArray(), $data['start'], $data['end']);
                break;

            default:
                $res = $this->deleteScheduleAllRelated($info);
                $this->deleteSystemSchedule($info->toArray());
                $this->changeNoticeStatus($entId, $info->toArray(), $data['start'], $data['end'], true);
        }

        $this->clearScheduleCache($userIds);
        return (bool)$res;
    }

    /**
     * 统一删除：模型 ->delete() 方式
     */
    private function deleteScheduleAllRelated($info): bool
    {
        return $this->transaction(function () use ($info) {
            // 主模型删除
            $info->delete();
            // 关联提醒 模型删除
            if ($info->remindData) {
                $info->remindData->delete();
            }
            // 关联任务
            $this->taskDao->delete(['pid' => $info->id]);
            // 关联用户 模型删除
            if ($info->scheduleUser) {
                collect($info->scheduleUser)->each->delete();
            }
            return true;
        });
    }

    /**
     * 修改日程状态
     * @throws BindingResolutionException
     */
    public function updateStatus(int $id, int $uid, int $entId, int $status, array $timeZone = []): bool
    {
        if (! $this->userDao->exists(['schedule_id' => $id, 'uid' => $uid])) {
            throw $this->exception('未找到相关记录');
        }
        $info = $this->dao->get($id, with: [
            'remind' => fn ($q) => $q->select(['id', 'sid', 'uniqued']),
        ])?->toArray();
        if (! $info) {
            throw $this->exception('未找到相关记录');
        }

        // 付款提醒
        if (in_array($info['cid'], [ScheduleEnum::TYPE_CLIENT_RENEW, ScheduleEnum::TYPE_CLIENT_RETURN])) {
            $period = [];
            if ($status == 3 && $info['cid'] == ScheduleEnum::TYPE_CLIENT_RENEW) {
                $period = $this->getNextPeriod($timeZone[0], $timeZone[1], $info['period'], $info['rate'], $info['days']);
            }
            $remindService = app()->get(RemindService::class);
            $remindService->updatePeriod($status, $info['remind']['uniqued'], [$timeZone[0], $period[0] ?? '']);
        }
        $where = [
            'pid'        => $id,
            'uid'        => $uid,
            'start_time' => $timeZone[0],
            'end_time'   => $timeZone[1],
        ];
        $save = [
            'pid'        => $id,
            'uid'        => $uid,
            'status'     => $status,
            'start_time' => $timeZone[0],
            'end_time'   => $timeZone[1],
        ];
        $this->clearScheduleCache($uid);
        $res = $this->taskDao->exists($where)
            ? (bool) $this->taskDao->update($where, $save)
            : (bool) $this->taskDao->create($save);

        if ($res) {
            $this->syncScheduleTodoInstance($uid, $id, $info, (int) $status, $timeZone[0], $timeZone[1]);
        }
        return $res;
    }

    private function syncScheduleTodoInstance(int $uid, int $id, array $info, int $status, string $startTime, string $endTime): void
    {
        try {
            app()->get(TodoItemService::class)->syncScheduleInstanceForUser(
                $uid,
                $id,
                (string) ($info['title'] ?? ''),
                $startTime,
                $endTime,
                $status,
                (int) ($info['all_day'] ?? 0)
            );
        } catch (\Throwable) {
            // 待办同步失败不能影响日程状态主流程.
        }
    }

    /**
     * 获取日程信息.
     * @param mixed $where
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function scheduleInfo(int $id, int $userId, array $field = ['*'], $where = []): array
    {
        if (! $this->userDao->exists(['schedule_id' => $id, 'uid' => $userId]) && ! $this->dao->exists(['id' => $id, 'uid' => $userId])) {
            throw $this->exception('未找到相关记录');
        }
        $info = Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            md5('schedule_' . $id . json_encode($field) . json_encode($where)),
            $this->cacheTtl,
            function () use ($id, $field, $where, $userId) {
                $info = $this->dao->get($id, $field, [
                    'master' => fn ($query) => $query->select(['admin.id', 'name', 'avatar', 'phone']),
                    'user'   => fn ($query) => $query->select(['admin.id', 'name', 'avatar', 'phone']),
                    'task'   => fn ($query) => $query->where('schedule_task.start_time', $where['start_time'])->where('schedule_task.end_time', $where['end_time'])->select(['uid', 'status', 'pid', 'schedule_task.updated_at']),
                    'type'   => fn ($query) => $query->select(['id', 'name', 'color', 'info']),
                    'remind' => fn ($query) => $query->select(['sid', 'remind_day', 'remind_time']),
                ])?->toArray();
                if ($info) {
                    $info['linkName'] = match ($info['cid']) {
                        ScheduleEnum::TYPE_CLIENT_TRACK => app()->get(CustomerService::class)->value($info['link_id'], 'customer_name'),
                        ScheduleEnum::TYPE_CLIENT_RENEW, ScheduleEnum::TYPE_CLIENT_RETURN => app()->get(OrderService::class)->value($info['link_id'], 'contract_name'),
                        default => '',
                    };
                    $info['finish'] = $this->getScheduleStatus($userId, $info['uid'], $id, $where['start_time'], $where['end_time']);
                    if ($info['is_remind']) {
                        $info['remindInfo'] = $this->getRemindText($info['start_time'], Carbon::parse($info['remind']['remind_day'] . ' ' . $info['remind']['remind_time'], $this->timeZone)->toDateTimeString());
                    } else {
                        $info['remindInfo'] = [
                            'ident' => -1,
                            'text'  => '不提醒',
                        ];
                    }
                }
                return json_encode($info, JSON_UNESCAPED_UNICODE);
            }
        );
        return json_decode($info, true);
    }

    public function getEntListCache(): mixed
    {
        return Cache::tags([CacheEnum::TAG_SCHEDULE])->remember('schedule_ent_list', $this->cacheTtl, fn () => $this->remindDao->getEntList());
    }

    public function getEntCountCache(array $where): mixed
    {
        return Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            md5('schedule_ent_count_' . json_encode($where)),
            $this->cacheTtl,
            fn () => $this->remindDao->count($where)
        );
    }

    public function scheduleTimer(array $where, int $page, int $limit): void
    {
        $list = Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            md5('schedule_list_' . json_encode($where) . '_' . $page . '_' . $limit),
            $this->cacheTtl,
            fn () => $this->remindDao->setDefaultSort('id')->selectModel($where, with: ['schedule'])->forPage($page, $limit)->get()?->toArray(),
        );
        $now = now();
        foreach ($list as $item) {
            $time = $item['remind_day'] . ' ' . $item['remind_time'];
            // 结束的不再提醒
            if ($item['end_time'] != null && datetime_timestamp($item['end_time']) < $now->timestamp) {
                break;
            }
            $timeNowPeriod = now()->setTimeFromTimeString($time);
            // 提醒日期比当前日期大，没到提醒时间
            if ($timeNowPeriod->timestamp > $now->timestamp) {
                break;
            }
            $item['days'] = $item['days'] ? (is_string($item['days']) ? json_decode($item['days'], true) : $item['days']) : [];
            switch ($item['period']) {
                case ScheduleEnum::REPEAT_NOT:
                case ScheduleEnum::REPEAT_DAY:
                    $diffNum = $now->diffInDays($timeNowPeriod);
                    // 当前时间和提醒时间相差的天数取余频率等于0证明达到提醒
                    if (($diffNum % $item['rate']) == 0) {
                        // 按照天进行提醒
                        $dayTime = $timeNowPeriod->addDays($diffNum);
                        if ($dayTime->day == $now->day && $dayTime->timestamp == $now->timestamp) {
                            $this->sendMessage(1, $item['schedule'], $item['uid']);
                        }
                    }
                    break;
                case ScheduleEnum::REPEAT_WEEK:
                    $diffNum = $now->diffInWeeks($timeNowPeriod);
                    // 当前时间和提醒时间相差的天数取余频率等于0证明达到提醒
                    if (($diffNum % $item['rate']) == 0) {
                        $dayTime   = $timeNowPeriod->addWeeks($diffNum);
                        $dayOfWeek = $dayTime->dayOfWeekIso;
                        // 在周几内提醒,并且本周当前的时间等于设置的时间
                        if (in_array($dayOfWeek, $item['days']) && $dayTime->timestamp == $now->timestamp) {
                            $this->sendMessage(1, $item['schedule'], $item['uid']);
                        }
                    }
                    break;
                case ScheduleEnum::REPEAT_MONTH:
                    $diffNum = $now->diffInMonths($timeNowPeriod);
                    // 当前时间和提醒时间相差的天数取余频率等于0证明达到提醒
                    if (($diffNum % $item['rate']) == 0) {
                        $dayTime = $timeNowPeriod->addMonths($diffNum);
                        $day     = $dayTime->day;
                        if ($day > 10) {
                            $day = '0' . $day;
                        }
                        // 每月几号进行提醒
                        if (in_array($day, $item['days']) && $dayTime->timestamp == $now->timestamp) {
                            $this->sendMessage(1, $item['schedule'], $item['uid']);
                        }
                    }
                    break;
                case ScheduleEnum::REPEAT_YEAR:
                    $diffNum = $now->diffInYears($timeNowPeriod);
                    // 当前时间和提醒时间相差的天数取余频率等于0证明达到提醒
                    if (($diffNum % $item['rate']) == 0) {
                        $dayTime = $timeNowPeriod->addYears($item['rate']);
                        $day     = $dayTime->day;
                        if ($day > 10) {
                            $day = '0' . $day;
                        }
                        // 每月几号进行提醒
                        if (in_array($day, $item['days']) && $dayTime->timestamp == $now->timestamp) {
                            $this->sendMessage(1, $item['schedule'], $item['uid']);
                        }
                    }
                    break;
            }
        }
    }

    /**
     * 发送消息.
     *
     * @param mixed $uid
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function sendMessage(int $entid, array $info, $uid = 0)
    {
        if ($uid) {
            $this->remindDao->update(['sid' => $info['id'], 'uid' => $uid], ['last_time' => date('Y-m-d H:i:s'), 'is_remind' => DB::raw('is_remind + 1')]);
            $userInfo = app()->get(AdminService::class)->get($info['uid']);
            if ($info['end_time']) {
                Cache::tags([CacheEnum::TAG_SCHEDULE])->flush();
            }
            switch ((int) $info['cid']) {
                case 1:// 个人提醒
                    event(new SystemMessageEvent(
                        type: NoticeEnum::DEALT_PRESON_WORK_TYPE,
                        params: [
                            '待办内容' => $info['title'],
                            '备注'     => $info['title'],
                            '创建人'   => $userInfo['name'] ?? '',
                            '创建时间' => $info['created_at'],
                        ],
                        receiverIds: (int) $uid,
                        other: [
                            'id'         => $info['id'],
                            'start_time' => $info['start_time'],
                            'end_time'   => $info['end_time'],
                        ],
                        linkId: $info['id'],
                        linkStatus: 0,
                    ));
                    break;
                case 2:// 客户跟进
                    $followInfo = app()->get(FollowUpService::class)->get(['eid' => $info['link_id']], ['id', 'eid'], ['client'])?->toArray();
                    event(new SystemMessageEvent(
                        type: NoticeEnum::DEALT_CLIENT_WORK_TYPE,
                        params: [
                            '待办内容' => $info['title'],
                            '备注'     => $info['content'],
                            '创建人'   => $userInfo['name'] ?? '',
                            '创建时间' => $info['created_at'],
                            '客户名称' => $followInfo['client']['name'] ?? '',
                        ],
                        receiverIds: (int) $uid,
                        other: ['id' => $info['link_id']],
                        linkId: $followInfo['eid'],
                        linkStatus: 0
                    ));
                    break;
                case 3:// 续费提醒
                case 4:// 回款
                    $clientRemind = app()->get(RemindService::class)->get(['cid' => $info['link_id']], ['id', 'eid', 'cid'], ['client', 'treaty'])?->toArray();
                    event(new SystemMessageEvent(
                        type: NoticeEnum::DEALT_MONEY_WORK_TYPE,
                        params: [
                            '待办内容'         => $info['title'],
                            '待办类型'         => $info['cid'] == ScheduleEnum::TYPE_CLIENT_RENEW ? '续费' : '回款',
                            '备注'             => $info['content'],
                            '创建人'           => $userInfo['name'] ?? '',
                            '创建时间'         => $info['created_at'],
                            '客户名称'         => $clientRemind['client']['name'] ?? '',
                            '关联订单名称' => $clientRemind['treaty']['title'] ?? '',
                            '订单金额'     => $clientRemind['treaty']['price'] ?? '',
                            '订单开始时间' => $clientRemind['treaty']['start_date'] ?? '',
                            '订单结束时间' => $clientRemind['treaty']['end_date'] ?? '',
                        ],
                        receiverIds: (int) $uid,
                        other: ['id' => $info['link_id']],
                        linkId: $clientRemind['cid'],
                        linkStatus: 0
                    ));
                    break;
                default:
                    event(new SystemMessageEvent(
                        type: NoticeEnum::DEALT_PRESON_WORK_TYPE,
                        params: [
                            '待办内容' => $info['title'],
                            '备注'     => $info['title'],
                            '创建人'   => $userInfo['name'] ?? '',
                            '创建时间' => $info['created_at'],
                        ],
                        receiverIds: (int) $uid,
                        other: ['id' => $info['id']],
                        linkId: $info['id'],
                        linkStatus: 0,
                    ));
            }
        }
    }

    /**
     * 通过提醒删除日程.
     * @param mixed $unique
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function deleteRemind(int $uid, string $unique): bool
    {
        $remindInfo = toArray($this->remindDao->get(['uid' => $uid, 'uniqued' => $unique], ['id', 'sid']));
        if (! $remindInfo) {
            return true;
        }
        return $this->transaction(function () use ($remindInfo) {
            $res1 = $this->remindDao->delete($remindInfo['id']);
            $res2 = $this->dao->delete($remindInfo['sid']);
            return $res1 && $res2;
        });
    }

    /**
     * 通过关联ID删除日程.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function deleteFromLinkId(int $uid, int $linkId, int $type): bool
    {
        $scheduleId = $this->dao->column(['uid' => $uid, 'link_id' => $linkId, 'cid' => $type], 'id');
        if (! $scheduleId) {
            return true;
        }
        return $this->transaction(function () use ($scheduleId) {
            $res1 = $this->dao->delete(['id' => $scheduleId]);
            $res2 = $this->remindDao->delete(['sid' => $scheduleId]);
            $this->userDao->delete(['schedule_id' => $scheduleId]);
            $this->taskDao->delete(['pid' => $scheduleId]);
            return $res1 && $res2;
        });
    }

    /**
     * 获取汇报日程完成记录.
     * @throws BindingResolutionException
     */
    public function dailyCompleteRecord(string $uuid, int $type): array
    {
        $list = toArray($this->dao->getWithTask([
            'uid'        => uuid_to_uid($uuid),
            'status'     => 3,
            'start_time' => match ($type) {
                1       => Carbon::today($this->timeZone)->floorWeek()->toDateTimeString(),
                2       => Carbon::today($this->timeZone)->firstOfMonth()->toDateTimeString(),
                default => Carbon::today($this->timeZone)->toDateTimeString(),
            },
            'end_time' => match ($type) {
                1       => Carbon::today($this->timeZone)->endOfWeek()->endOfDay()->toDateTimeString(),
                2       => Carbon::today($this->timeZone)->endOfMonth()->endOfDay()->toDateTimeString(),
                default => Carbon::today($this->timeZone)->endOfDay()->toDateTimeString(),
            },
        ]));
        return $list ? array_column($list, 'title') : [];
    }

    /**
     * 获取日程未完成量列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function scheduleCount(int $userId, int $entId, string $start, string $end, array $cid = [], int $period = 3): array
    {
        $detailKey = md5($userId . $period . json_encode($cid) . $start . $end . 'schedule_count');
        $result    = Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            $detailKey,
            (int) sys_config('system_cache_ttl', 3600),
            function () use ($period, $start, $end, $userId) {
                $field    = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'rate', 'days', 'link_id', 'fail_time'];
                $list     = $this->dao->select(['exists_uid' => $userId], $field, ['taskOne' => fn ($q) => $q->where('schedule_task.uid', $userId)])?->toArray();
                $data     = $this->checkPeriod($period, $list, $start, $end, $userId);
                $timeZone = array_map(fn ($val) => $val->toDateString(), CarbonPeriod::create(Carbon::parse($start, $this->timeZone)->toDateTimeString(), Carbon::parse($end, $this->timeZone)->toDateTimeString())->toArray());
                $restMap  = $result = [];
                // 日历配置
                $getRestList = app()->get(CalendarConfigService::class)->getRestList(Carbon::parse($end, config('app.timezone'))->format('Y'));
                foreach ($getRestList as $rest) {
                    $restMap[$rest] = 1;
                }
                foreach ($timeZone as $time) {
                    $count = $no_submit = 0;
                    if (Carbon::parse($time)->endOfDay()->timestamp <= today()->endOfDay()->timestamp) {
                        foreach ($data as $val) {
                            $have = $this->hasOverlap(Carbon::parse($time)->timestamp, Carbon::parse($time)->endOfDay()->timestamp, Carbon::parse($val['start_time'])->timestamp, Carbon::parse($val['end_time'])->timestamp);
                            if ($have) {
                                ++$count;
                                if ($val['finish'] != 3 && $val['finish'] != 2) {
                                    ++$no_submit;
                                }
                            }
                        }
                    }

                    $result[] = [
                        'time'      => $time,
                        'no_submit' => $count ? $no_submit : -1,
                        'is_rest'   => $restMap[$time] ?? 0,
                    ];
                }
                return json_encode($result, JSON_UNESCAPED_UNICODE);
            }
        );
        return json_decode($result, true);
    }

    /**
     * 获取日程重复频率.
     */
    public function getScheduleDays(int $types, int $period, string $time): array
    {
        $days                = [];
        $carbon              = Carbon::parse($time, config('app.timezone'));
        $types == 1 && $days = match ($period) {
            1       => [$carbon->dayOfWeekIso],
            2       => [$carbon->format('d')],
            default => []
        };
        return $days;
    }

    /**
     * 根据关联业务删除日程.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function delScheduleByLinkId(int $linkId, array|int $cid): void
    {
        $this->dao->select(['link_id' => $linkId, 'cid' => $cid], ['id', 'uid', 'cid', 'link_id'])->each(function ($item) use ($linkId) {
            $this->deleteFromLinkId((int) $item->uid, $linkId, (int) $item->cid);
        });
    }

    /**
     * 日程回复列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function replays(array $where): array
    {
        $field       = ['id', 'uid', 'pid', 'reply_id', 'to_uid', 'content', 'created_at'];
        $mainReplies = collect($this->replyDao->select(
            $where + ['reply_id' => 0],
            $field,
            with: [
                'from_user' => fn ($q) => $q->select(['id', 'name', 'avatar']),
                'files'     => fn ($query) => $query->select(['id', 'att_dir as src', 'att_size as size', 'relation_id', 'real_name as name']),
            ]
        ));
        if ($mainReplies->isEmpty()) {
            return [];
        }
        $mainReplyIds  = $mainReplies->pluck('id')->all();
        $allSubReplies = collect($this->replyDao->select(
            $where + ['reply_id' => $mainReplyIds],
            $field,
            with: [
                'from_user' => fn ($q) => $q->select(['id', 'name', 'avatar']),
                'to_user'   => fn ($q) => $q->select(['id', 'name', 'avatar']),
                'files'     => fn ($query) => $query->select(['id', 'att_dir as src', 'att_size as size', 'relation_id', 'real_name as name']),
            ]
        ));
        $mainReplies = $mainReplies->map(function ($mainReply) {
            // 将 files 转为集合，处理 src 链接
            $mainReply['files'] = collect($mainReply['files'] ?? [])->map(function ($file) {
                $file['src'] = $file['src'] ? link_file($file['src']) : '';
                return $file;
            })->all(); // 转回数组保持原结构
            return $mainReply;
        });
        $allSubReplies = $allSubReplies->map(function ($subReply) {
            $subReply['files'] = collect($subReply['files'] ?? [])->map(function ($file) {
                $file['src'] = $file['src'] ? link_file($file['src']) : '';
                return $file;
            })->all();
            return $subReply;
        });
        $subRepliesGrouped = $allSubReplies->groupBy('reply_id')->map->all();
        // 为主回复分配子回复
        return $mainReplies->map(function ($mainReply) use ($subRepliesGrouped) {
            $mainReply['children'] = $subRepliesGrouped->get($mainReply['id'], []);
            return $mainReply;
        })->all();
    }

    /**
     * 保存评价.
     * @throws BindingResolutionException
     */
    public function saveReply(string $uuid, int $entId, array $data)
    {
        $data['uid'] = uuid_to_uid($uuid, $entId);
        $fileId      = $data['files'];
        unset($data['files']);
        $saved = $this->replyDao->create($data);
        if ($saved && $fileId) {
            app()->get(AttachService::class)->saveRelation($fileId, $uuid, $saved->id, AttachEnum::RELATION_TYPE_SCHEDULE_REPLY);
        }
        return $saved;
    }

    /**
     * 删除日程评价.
     * @return mixed
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function delReply(int $id, string $uuid, int $entId)
    {
        $uid = uuid_to_uid($uuid, $entId);
        if (! $this->replyDao->exists(['id' => $id, 'uid' => $uid])) {
            throw $this->exception('未找到可删除评价');
        }
        return $this->transaction(function () use ($id) {
            $this->replyDao->delete($id);
            $this->replyDao->delete(['reply_id' => $id]);
            app()->get(AttachService::class)->delRelationFiles($id, AttachEnum::RELATION_TYPE[AttachEnum::RELATION_TYPE_SCHEDULE_REPLY]);
            return true;
        });
    }

    /**
     * 下个工作日待办标题数就.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getNextWorkDayPlan(string $uuid): array
    {
        return array_column($this->getNextWorkWorkByCache($uuid), 'title');
    }

    /**
     * 下个工作日待办数据.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getNextWorkWorkByCache(string $uuid)
    {
        $uid        = uuid_to_uid($uuid);
        $workDay    = app()->get(AttendanceArrangeService::class)->getNextArrangeDayByUid($uid, Carbon::now($this->timeZone)->toDateString());
        $workDayObj = Carbon::parse($workDay, $this->timeZone);
        $start      = $workDayObj->startOfDay()->toDateTimeString();
        $end        = $workDayObj->endOfDay()->toDateTimeString();

        $detailKey = md5($uid . $start . $end . 'schedule');
        $res       = Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            $detailKey,
            (int) sys_config('system_cache_ttl', 3600),
            function () use ($start, $end, $uid) {
                $field = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'rate', 'days', 'link_id', 'fail_time'];
                $list  = toArray($this->dao->select(['id' => array_unique(array_merge(
                    toArray($this->userDao->column(['uid' => $uid], 'schedule_id')),
                    $this->dao->column(['uid' => $uid], 'id')
                ))], $field));

                $data = $this->checkPeriod(1, $list, $start, $end, $uid);
                return json_encode(array_filter($data, fn ($item) => in_array($item['finish'], [-1, 0, 1])), JSON_UNESCAPED_UNICODE);
            }
        );
        return json_decode($res, true);
    }

    /**
     * 按日期获取日程列表.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function scheduleDateList(int $uid, int $entId, string $start, string $end, array $cid, int $period = 1): array
    {
        $res = Cache::tags([CacheEnum::TAG_SCHEDULE])->remember(
            md5($uid . $period . json_encode($cid) . $start . $end . 'schedule_date'),
            (int) sys_config('system_cache_ttl', 3600),
            function () use ($start, $end, $uid, $cid) {
                $assistUid    = $this->userDao->column(['uid' => $uid], 'schedule_id');
                $masterUid    = $this->dao->column(['uid' => $uid], 'id');
                $where['id']  = array_unique(array_merge($assistUid, $masterUid));
                $where['cid'] = $cid;
                if (in_array(ScheduleEnum::TYPE_PERSONAL, $cid)) {
                    $assistCid = $this->dao->column(['id' => $assistUid], 'cid') ?: [];
                    $userCid   = $this->typeDao->column(['user_id' => $uid], 'id') ?: [];
                    $assistCid = array_values(array_filter($assistCid, function ($item) use ($userCid) {
                        return ! in_array($item, [
                            ScheduleEnum::TYPE_PERSONAL,
                            ScheduleEnum::TYPE_CLIENT_TRACK,
                            ScheduleEnum::TYPE_CLIENT_RENEW,
                            ScheduleEnum::TYPE_CLIENT_RETURN,
                            ScheduleEnum::TYPE_REPORT_RENEW,
                        ]) && ! in_array($item, $userCid);
                    }));
                    $where['cid'] = array_unique(array_merge($cid, $assistCid));
                }
                $field = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'rate', 'days', 'link_id', 'fail_time'];
                $list  = $this->listHandler($this->dao->select($where, $field, [
                    'master' => fn ($query) => $query->select(['admin.id', 'name', 'avatar', 'phone']),
                    'type'   => fn ($query) => $query->select(['id', 'name', 'color', 'info']),
                    'user'   => fn ($query) => $query->select(['admin.id', 'name', 'avatar', 'phone']),
                    'remind' => fn ($query) => $query->select(['schedule_remind.uniqued', 'schedule_remind.sid', 'schedule_remind.remind_day', 'schedule_remind.remind_time']),
                ])?->toArray());
                $data = [];
                do {
                    $data[] = [
                        'date' => $start,
                        'list' => $this->getDaySchedule($list, $start, $uid),
                    ];
                    $start = Carbon::make($start)->addDay()->toDateString();
                } while (Carbon::parse($start)->isBefore($end) || $start == $end);
                return json_encode($data, JSON_UNESCAPED_UNICODE);
            }
        );
        return json_decode($res, true);
    }

    /**
     * 按单个日程展开日期列表，用于待办增量同步，避免编辑一条日程时重扫用户全部日程.
     *
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function scheduleDateListByScheduleId(int $uid, int $scheduleId, string $start, string $end): array
    {
        $field = ['id', 'uid', 'cid', 'color', 'title', 'content', 'all_day', 'start_time', 'end_time', 'period', 'rate', 'days', 'link_id', 'fail_time'];
        $list  = toArray($this->dao->select(['id' => $scheduleId], $field));
        if (! $list) {
            return [];
        }

        $data   = [];
        $day    = Carbon::parse($start, $this->timeZone)->startOfDay();
        $endDay = Carbon::parse($end, $this->timeZone)->startOfDay();
        while ($day->lessThanOrEqualTo($endDay)) {
            $date   = $day->toDateString();
            $data[] = [
                'date' => $date,
                'list' => $this->getDaySchedule($list, $date, $uid),
            ];
            $day->addDay();
        }

        return $data;
    }

    /**
     * 日程类型表单规则.
     */
    protected function getTypeForm(Collection $collection): array
    {
        return [
            Form::input('name', '类型名称', $collection->get('name'))->placeholder('请输入类型名称')->maxlength(20)->required(),
            Form::color('color', '颜色标识', $collection->get('color', '#1890FF'))->required(),
            Form::textarea('info', '类型描述', $collection->get('info', ''))->placeholder('请输入类型描述')
                ->maxlength(256)->rows(4),
        ];
    }

    protected function checkPeriod($period, $list, $start, $end, $userId)
    {
        return match ($period) {
            1       => $this->getDaySchedule($list, $start, $userId),
            2       => $this->getWeekSchedule($list, $start, $userId),
            3       => $this->getMonthSchedule($list, $start, $end, $userId),
            default => [],
        };
    }

    /**
     * 日重复提醒.
     * @param mixed $list
     * @param mixed $start
     * @param mixed $userId
     * @return array
     */
    protected function getDaySchedule($list, $start, $userId)
    {
        $timeZone = [Carbon::parse($start, $this->timeZone)->toDateString()];
        $data     = [];
        foreach ($timeZone as $day) {
            if ($list) {
                foreach ($list as &$value) {
                    [$isHave, $status, $startTime, $endTime] = $this->haveSchedule($value, $day, $userId);
                    if ($isHave) {
                        $value['start_time'] = $startTime;
                        $value['end_time']   = $endTime;
                        $value['finish']     = $status;
                        $unique              = md5($startTime . $value['id'] . $endTime);
                        $data[$unique]       = $value;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 周重复提醒.
     * @param mixed $list
     * @param mixed $start
     * @param mixed $userId
     * @return array
     */
    protected function getWeekSchedule($list, $start, $userId)
    {
        $timeZone = CarbonPeriod::create(Carbon::parse($start, $this->timeZone)->startOfWeek()->toDateString(), Carbon::parse($start, $this->timeZone)->endOfWeek()->toDateString())->toArray();
        $data     = [];
        foreach ($timeZone as $day) {
            $day = $day->toDateString();
            if ($list) {
                foreach ($list as &$value) {
                    [$isHave, $status, $startTime, $endTime] = $this->haveSchedule($value, $day, $userId);
                    if ($isHave) {
                        $value['start_time'] = $startTime;
                        $value['end_time']   = $endTime;
                        $value['finish']     = $status;
                        $unique              = md5($startTime . $value['id'] . $endTime);
                        $data[$unique]       = $value;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 月重复提醒.
     *
     * @param mixed $userId
     * @param mixed $list
     * @param mixed $start
     * @param mixed $end
     *
     * @return array
     */
    protected function getMonthSchedule($list, $start, $end, $userId)
    {
        $timeZone = CarbonPeriod::create(Carbon::parse($start, $this->timeZone)->toDateString(), Carbon::parse($end, $this->timeZone)->toDateString())->toArray();
        $data     = [];
        foreach ($timeZone as $day) {
            if ($list) {
                foreach ($list as &$value) {
                    [$isHave, $status, $startTime, $endTime] = $this->haveSchedule($value, $day->toDateString(), $userId);
                    if ($isHave) {
                        $value['start_time'] = $startTime;
                        $value['end_time']   = $endTime;
                        $value['finish']     = $status;
                        $unique              = md5($startTime . $value['id'] . $endTime);
                        $data[$unique]       = $value;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 指定日期是否有日程提醒.
     * @param mixed $userId
     * @param mixed $schedule
     * @param mixed $day
     * @return bool
     */
    protected function haveSchedule($schedule, $day, $userId): array
    {
        $isHava    = false;
        $status    = -1;
        $startTime = Carbon::parse($schedule['start_time'], $this->timeZone);
        $endTime   = Carbon::parse($schedule['end_time'], $this->timeZone);
        if ($schedule['days'] && is_string($schedule['days'])) {
            $schedule['days'] = json_decode($schedule['days'], true);
        }
        if (! $schedule['fail_time']) {
            $failTime = Carbon::parse($schedule['fail_time'], $this->timeZone)->addYears(5)->timestamp;
        } else {
            $failTime = Carbon::parse($schedule['fail_time'], $this->timeZone)->timestamp;
        }
        $dayTime = Carbon::parse($day, $this->timeZone)->endOfDay();
        $start   = $startTime->copy()->toDateTimeString();
        $end     = $endTime->copy()->toDateTimeString();
        switch ($schedule['period']) {
            case ScheduleEnum::REPEAT_DAY:// 天重复
                $period = bcdiv(bcsub((string) $dayTime->timestamp, (string) $startTime->copy()->startOfDay()->timestamp), bcmul((string) $schedule['rate'], '86400'));
                if ((int) $period >= 0) {
                    $start = $startTime->copy()->addDays($period * $schedule['rate']);
                    $end   = $endTime->copy()->addDays($period * $schedule['rate']);
                    if ($this->hasOverlap($dayTime->startOfDay()->timestamp, $dayTime->endOfDay()->timestamp, $start->timestamp, $end->timestamp) && $failTime >= $dayTime->endOfDay()->timestamp) {
                        $status = $this->getScheduleStatus($userId, $schedule['uid'], $schedule['id'], $start, $end);
                        $isHava = true;
                    }
                    $start = $start->toDateTimeString();
                    $end   = $end->toDateTimeString();
                }
                break;
            case ScheduleEnum::REPEAT_WEEK:// 周重复
                $period = bcdiv(bcsub((string) $dayTime->timestamp, (string) $startTime->copy()->startOfDay()->timestamp), bcmul((string) $schedule['rate'], '604800'));
                if ($period >= 0) {
                    foreach ($schedule['days'] as $v) {
                        $dayOfWeek = $startTime->dayOfWeekIso;
                        if ($dayTime->dayOfWeekIso == (int) $v) {
                            if ($dayOfWeek > $v) {
                                $period = $period + 1;
                                $start  = $startTime->copy()->addWeeks($period * $schedule['rate'])->subDays($dayOfWeek - $v);
                                $end    = $endTime->copy()->addWeeks($period * $schedule['rate'])->subDays($dayOfWeek - $v);
                            } else {
                                $start = $startTime->copy()->addWeeks($period * $schedule['rate'])->addDays($v - $dayOfWeek);
                                $end   = $endTime->copy()->addWeeks($period * $schedule['rate'])->addDays($v - $dayOfWeek);
                            }
                            if ($this->hasOverlap($dayTime->startOfDay()->timestamp, $dayTime->endOfDay()->timestamp, $start->timestamp, $end->timestamp) && $failTime >= $dayTime->endOfDay()->timestamp) {
                                $status = $this->getScheduleStatus($userId, $schedule['uid'], $schedule['id'], $start, $end);
                                $isHava = true;
                            }
                            $start = $start->toDateTimeString();
                            $end   = $end->toDateTimeString();
                            break;
                        }
                    }
                }
                break;
            case ScheduleEnum::REPEAT_MONTH:// 月重复
                $period = bcdiv(bcsub($dayTime->format('m'), $startTime->copy()->format('m')), (string) $schedule['rate']);
                if ($period >= 0) {
                    foreach ($schedule['days'] as $v) {
                        $dayOfMonth = (int) $startTime->copy()->format('d');
                        if ($dayTime->format('d') == $v) {
                            if ($dayOfMonth > $v) {
                                $start = $startTime->copy()->addMonths($period * $schedule['rate'])->subDays($dayOfMonth - $v);
                                $end   = $endTime->copy()->addMonths($period * $schedule['rate'])->subDays($dayOfMonth - $v);
                            } else {
                                $start = $startTime->copy()->addMonths($period * $schedule['rate'])->addDays($v - $dayOfMonth);
                                $end   = $endTime->copy()->addMonths($period * $schedule['rate'])->addDays($v - $dayOfMonth);
                            }
                            if ($start->timestamp >= $startTime->copy()->timestamp
                                && $this->hasOverlap($dayTime->startOfDay()->timestamp, $dayTime->endOfDay()->timestamp, $start->timestamp, $end->timestamp)
                                && $failTime >= $dayTime->endOfDay()->timestamp) {
                                $status = $this->getScheduleStatus($userId, $schedule['uid'], $schedule['id'], $start, $end);
                                $isHava = true;
                            }
                            $start = $start->toDateTimeString();
                            $end   = $end->toDateTimeString();
                            break;
                        }
                    }
                    if ($this->hasOverlap($dayTime->startOfDay()->timestamp, $dayTime->endOfDay()->timestamp, $startTime->copy()->timestamp, $endTime->copy()->timestamp) && $failTime >= $startTime->copy()->timestamp) {
                        $status = $this->getScheduleStatus($userId, $schedule['uid'], $schedule['id'], $start, $end);
                        $isHava = true;
                        break;
                    }
                }
                break;
            case ScheduleEnum::REPEAT_YEAR:// 年重复
                $period = bcdiv(bcsub($dayTime->format('y'), $startTime->copy()->format('y')), (string) $schedule['rate']);
                if ($period >= 0) {
                    $start = $startTime->copy()->addYears($period * $schedule['rate']);
                    $end   = $endTime->copy()->addYears($period * $schedule['rate']);
                    if ($start->timestamp >= $startTime->copy()->timestamp
                        && $this->hasOverlap($dayTime->startOfDay()->timestamp, $dayTime->endOfDay()->timestamp, $start->timestamp, $end->timestamp)
                        && $failTime >= $dayTime->endOfDay()->timestamp) {
                        $status = $this->getScheduleStatus($userId, $schedule['uid'], $schedule['id'], $start, $end);
                        $isHava = true;
                    }
                    $start = $start->toDateTimeString();
                    $end   = $end->toDateTimeString();
                }
                break;
            default:
                if ($this->hasOverlap($dayTime->startOfDay()->timestamp, $dayTime->endOfDay()->timestamp, $startTime->copy()->timestamp, $endTime->copy()->timestamp) && $failTime >= $startTime->copy()->timestamp) {
                    $status = $this->getScheduleStatus($userId, $schedule['uid'], $schedule['id'], $start, $end);
                    $isHava = true;
                }
        }
        return [$isHava, $status, $start, $end];
    }

    /**
     * 获取日程状态
     * @param mixed $userId
     * @param mixed $masterId
     * @param mixed $scheduleId
     * @param mixed $start
     * @param mixed $end
     * @return null|int|mixed
     * @throws BindingResolutionException
     */
    protected function getScheduleStatus($userId, $masterId, $scheduleId, $start, $end)
    {
        $status = $this->taskDao->value([
            'pid'        => $scheduleId,
            'start_time' => $start,
            'end_time'   => $end,
            'uid'        => $userId,
        ], 'status');
        if (is_null($status)) {
            if ($userId == $masterId) {
                if (! $this->userDao->exists(['uid' => $userId, 'schedule_id' => $scheduleId])) {
                    return 3;
                }
                return 1;
            }
            return -1;
        }
        return $status;
    }

    /**
     * 处理列表数据.
     * @param mixed $list
     * @return array|mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    protected function listHandler($list = [])
    {
        if (! $list) {
            return [];
        }

        // 获取关联数据
        foreach ($list as &$item) {
            $uniqued = $item['remind']['uniqued'] ?? '';
            if ($uniqued) {
                $item['relation'] = match ($item['cid']) {
                    2 => toArray(app()->get(FollowUpService::class)->get(['uniqued' => $uniqued, 'status' => 0, 'types' => 1], ['id as follow_id'])),
                    3, 4 => toArray(app()->get(RemindService::class)->get(['uniqued' => $uniqued, 'status' => 0], ['id as remind_id', 'bill_id'])),
                    default => null
                };
            }
            unset($item['remind']['uniqued']);
        }
        return $list;
    }

    private function clearScheduleCache(array|int $userIds): void
    {
        $userIds = is_array($userIds) ? $userIds : [$userIds];
        if ($userIds) {
            Cache::tags([CacheEnum::TAG_SCHEDULE])->flush();
        }
    }

    private function updateTime(int $id, string $startTime = '', string $endTime = '', ?string $failTime = '')
    {
        if ($startTime && $endTime && $failTime) {
            $res1 = $this->dao->update($id, [
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'fail_time'  => Carbon::parse($failTime, $this->timeZone)->endOfDay()->toDateTimeString(),
            ]);
            $res2 = $this->remindDao->update(['sid' => $id], ['end_time' => $failTime]);
            return $res1 && $res2;
        }
        if ($failTime) {
            $res1 = $this->dao->update($id, ['fail_time' => Carbon::parse($failTime, $this->timeZone)->endOfDay()->toDateTimeString()]);
            $res2 = $this->remindDao->update(['sid' => $id], ['end_time' => $failTime]);
            return $res1 && $res2;
        }
        return false;
    }

    /**
     * 获取上个周期的截止时间.
     * @param mixed $oldStart
     * @param mixed $newStart
     * @param mixed $period
     * @param mixed $rate
     * @param mixed $days
     */
    private function getPreviousPeriod($oldStart, $newStart, $period, $rate, $days = null): string
    {
        $failTime = $newStart;
        $days     = $days ? array_map('intval', $days) : [];
        switch ($period) {
            case ScheduleEnum::REPEAT_DAY:
                $failTime = Carbon::parse($newStart, $this->timeZone)->subDays($rate)->toDateTimeString();
                break;
            case ScheduleEnum::REPEAT_WEEK:
                rsort($days, SORT_NUMERIC);
                $day = Carbon::parse($newStart, $this->timeZone)->dayOfWeekIso;
                if ($day <= min($days)) {
                    $failDay  = Carbon::parse($oldStart, $this->timeZone)->subWeek()->startOfWeek()->addDays(max($days))->toDateString();
                    $failTime = Carbon::parse($oldStart, $this->timeZone)->toTimeString();
                    $failTime = Carbon::parse($failDay . ' ' . $failTime, $this->timeZone)->toDateTimeString();
                } else {
                    foreach ($days as $key => $val) {
                        if ($day > $val) {
                            $failDay  = Carbon::parse($oldStart, $this->timeZone)->startOfWeek()->addDays($val)->toDateString();
                            $failTime = Carbon::parse($oldStart, $this->timeZone)->toTimeString();
                            $failTime = Carbon::parse($failDay . ' ' . $failTime, $this->timeZone)->toDateTimeString();
                            break;
                        }
                        if ($day == $val) {
                            $failDay  = Carbon::parse($oldStart, $this->timeZone)->startOfWeek()->addDays($days[$key - 1])->toDateString();
                            $failTime = Carbon::parse($oldStart, $this->timeZone)->toTimeString();
                            $failTime = Carbon::parse($failDay . ' ' . $failTime, $this->timeZone)->toDateTimeString();
                            break;
                        }
                    }
                }
                break;
            case ScheduleEnum::REPEAT_MONTH:
                rsort($days, SORT_NUMERIC);
                $day = Carbon::parse($newStart, $this->timeZone)->format('d');
                if ($day <= min($days)) {
                    $failDay  = Carbon::parse($oldStart, $this->timeZone)->subMonth()->startOfMonth()->addDays(max($days))->toDateString();
                    $failTime = Carbon::parse($oldStart, $this->timeZone)->toTimeString();
                    $failTime = Carbon::parse($failDay . ' ' . $failTime, $this->timeZone)->toDateTimeString();
                } else {
                    foreach ($days as $key => $val) {
                        if ($day > $val) {
                            $failDay  = Carbon::parse($oldStart, $this->timeZone)->startOfMonth()->addDays($val)->toDateString();
                            $failTime = Carbon::parse($oldStart, $this->timeZone)->toTimeString();
                            $failTime = Carbon::parse($failDay . ' ' . $failTime, $this->timeZone)->toDateTimeString();
                            break;
                        }
                        if ($day == $val) {
                            $failDay  = Carbon::parse($oldStart, $this->timeZone)->startOfMonth()->addDays($days[$key - 1])->toDateString();
                            $failTime = Carbon::parse($oldStart, $this->timeZone)->toTimeString();
                            $failTime = Carbon::parse($failDay . ' ' . $failTime, $this->timeZone)->toDateTimeString();
                            break;
                        }
                    }
                }
                break;
            case ScheduleEnum::REPEAT_YEAR:
                $failTime = Carbon::parse($oldStart, $this->timeZone)->subYears($rate)->toDateTimeString();
                break;
        }
        return $failTime;
    }

    /**
     * 获取下个周期的时间.
     * @param mixed $newStart
     * @param mixed $newEnd
     * @param mixed $period
     * @param mixed $rate
     * @param mixed $days
     * @return array
     */
    private function getNextPeriod($newStart, $newEnd, $period, $rate, $days = [])
    {
        $startTime = $newStart;
        $endTime   = $newEnd;
        switch ($period) {
            case ScheduleEnum::REPEAT_DAY:
                $startTime = Carbon::parse($newStart, $this->timeZone)->addDays($rate)->toDateTimeString();
                $endTime   = Carbon::parse($newEnd, $this->timeZone)->addDays($rate)->toDateTimeString();
                break;
            case ScheduleEnum::REPEAT_WEEK:
                sort($days, SORT_NUMERIC);
                $day = Carbon::parse($newStart, $this->timeZone)->dayOfWeekIso;
                if ($day >= max($days)) {
                    $startDay  = Carbon::parse($newStart, $this->timeZone)->addWeeks($rate)->startOfWeek()->addDays(min($days))->subDay()->toDateString();
                    $startTime = Carbon::parse($startDay . ' ' . Carbon::parse($newStart, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                    $endDay    = Carbon::parse($newEnd, $this->timeZone)->addWeeks($rate)->startOfWeek()->addDays(min($days))->subDay()->toDateString();
                    $endTime   = Carbon::parse($endDay . ' ' . Carbon::parse($newEnd, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                } else {
                    foreach ($days as $key => $val) {
                        if ($day == $val) {
                            $startDay  = Carbon::parse($newStart, $this->timeZone)->startOfWeek()->addDays($days[$key + 1])->subDay()->toDateString();
                            $startTime = Carbon::parse($startDay . ' ' . Carbon::parse($newStart, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            $endDay    = Carbon::parse($newEnd, $this->timeZone)->startOfWeek()->addDays($days[$key + 1])->subDay()->toDateString();
                            $endTime   = Carbon::parse($endDay . ' ' . Carbon::parse($newEnd, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            break;
                        }
                        if ($day < $val) {
                            $startDay  = Carbon::parse($newStart, $this->timeZone)->startOfWeek()->addDays($val)->subDay()->toDateString();
                            $startTime = Carbon::parse($startDay . ' ' . Carbon::parse($newStart, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            $endDay    = Carbon::parse($newEnd, $this->timeZone)->startOfWeek()->addDays($val)->subDay()->toDateString();
                            $endTime   = Carbon::parse($endDay . ' ' . Carbon::parse($newEnd, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            break;
                        }
                    }
                }
                break;
            case ScheduleEnum::REPEAT_MONTH:
                sort($days, SORT_NUMERIC);
                $day = Carbon::parse($newStart, $this->timeZone)->format('d');
                if ($day >= max($days)) {
                    $startDay  = Carbon::parse($newStart, $this->timeZone)->addMonth()->startOfMonth()->addDays(min($days))->subDay()->toDateString();
                    $startTime = Carbon::parse($startDay . ' ' . Carbon::parse($newStart, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                    $endDay    = Carbon::parse($newEnd, $this->timeZone)->addMonth()->startOfMonth()->addDays(min($days))->subDay()->toDateString();
                    $endTime   = Carbon::parse($endDay . ' ' . Carbon::parse($newEnd, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                } else {
                    foreach ($days as $key => $val) {
                        if ($day == $val) {
                            $startDay  = Carbon::parse($newStart, $this->timeZone)->startOfMonth()->addDays($days[$key + 1])->subDay()->toDateString();
                            $startTime = Carbon::parse($startDay . ' ' . Carbon::parse($newStart, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            $endDay    = Carbon::parse($newEnd, $this->timeZone)->startOfMonth()->addDays($days[$key + 1])->subDay()->toDateString();
                            $endTime   = Carbon::parse($endDay . ' ' . Carbon::parse($newEnd, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            break;
                        }
                        if ($day < $val) {
                            $startDay  = Carbon::parse($newStart, $this->timeZone)->startOfMonth()->addDays($val)->subDay()->toDateString();
                            $startTime = Carbon::parse($startDay . ' ' . Carbon::parse($newStart, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            $endDay    = Carbon::parse($newEnd, $this->timeZone)->startOfMonth()->addDays($val)->subDay()->toDateString();
                            $endTime   = Carbon::parse($endDay . ' ' . Carbon::parse($newEnd, $this->timeZone)->toTimeString(), $this->timeZone)->toDateTimeString();
                            break;
                        }
                    }
                }
                break;
            case ScheduleEnum::REPEAT_YEAR:
                $startTime = Carbon::parse($newStart, $this->timeZone)->addYears($rate)->toDateTimeString();
                $endTime   = Carbon::parse($newEnd, $this->timeZone)->addYears($rate)->toDateTimeString();
                break;
        }
        return [$startTime, $endTime];
    }

    /**
     * 处理数据.
     */
    private function getRemindInfo(array $data): array
    {
        if (isset($data['remind_time']) && $data['remind_time']) {
            [$remind['remind_day'], $remind['remind_time']] = $this->getRemindTime($data['remind_time']);
        } else {
            [$remind['remind_day'], $remind['remind_time']] = $this->getRemindTime($data['start_time'], $data['remind']);
        }
        $remind['period']   = $data['period'];
        $remind['end_time'] = $data['fail_time'];
        $remind['uniqued']  = isset($data['uniqued']) && $data['uniqued'] ? $data['uniqued'] : md5(json_encode($data) . time());
        switch ((int) $data['period']) {
            case ScheduleEnum::REPEAT_DAY:
            case ScheduleEnum::REPEAT_YEAR:
                if (! $data['rate']) {
                    throw $this->exception('请选择重复频率');
                }
                $remind['rate'] = $data['rate'];
                $remind['days'] = json_encode($data['days']);
                break;
            case ScheduleEnum::REPEAT_WEEK:
            case ScheduleEnum::REPEAT_MONTH:
                if (! $data['days']) {
                    throw $this->exception('请选择提醒时间');
                }
                $remind['days'] = $data['days'] ?: null;
                $remind['rate'] = $data['rate'];
                break;
            default:
                $remind['end_time'] = $data['end_time'];
                $remind['rate']     = $data['rate'];
                $remind['days']     = null;
        }
        return $remind;
    }

    private function getRemindTime($startTime, $remind = 0): array
    {
        return match ((int) $remind) {
            1 => [
                Carbon::parse($startTime, $this->timeZone)->subMinutes(5)->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subMinutes(5)->toTimeString(),
            ],
            2 => [
                Carbon::parse($startTime, $this->timeZone)->subMinutes(15)->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subMinutes(15)->toTimeString(),
            ],
            3 => [
                Carbon::parse($startTime, $this->timeZone)->subMinutes(30)->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subMinutes(30)->toTimeString(),
            ],
            4 => [
                Carbon::parse($startTime, $this->timeZone)->subHour()->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subHour()->toTimeString(),
            ],
            5 => [
                Carbon::parse($startTime, $this->timeZone)->subHours(2)->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subHours(2)->toTimeString(),
            ],
            6 => [
                Carbon::parse($startTime, $this->timeZone)->subDay()->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subDay()->toTimeString(),
            ],
            7 => [
                Carbon::parse($startTime, $this->timeZone)->subDays(2)->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subDays(2)->toTimeString(),
            ],
            8 => [
                Carbon::parse($startTime, $this->timeZone)->subWeek()->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->subWeek()->toTimeString(),
            ],
            default => [
                Carbon::parse($startTime, $this->timeZone)->toDateString(),
                Carbon::parse($startTime, $this->timeZone)->toTimeString(),
            ],
        };
    }

    private function getRemindText($startTime, $remindTime): array
    {
        $remind = Carbon::parse($remindTime, $this->timeZone)->timestamp;
        return match ($remind) {
            Carbon::parse($startTime, $this->timeZone)->subMinutes(5)->timestamp => [
                'ident' => 1,
                'text'  => '提前5分钟',
            ],
            Carbon::parse($startTime, $this->timeZone)->subMinutes(15)->timestamp => [
                'ident' => 2,
                'text'  => '提前15分钟',
            ],
            Carbon::parse($startTime, $this->timeZone)->subMinutes(30)->timestamp => [
                'ident' => 3,
                'text'  => '提前30分钟',
            ],
            Carbon::parse($startTime, $this->timeZone)->subHour()->timestamp => [
                'ident' => 4,
                'text'  => '提前1小时',
            ],
            Carbon::parse($startTime, $this->timeZone)->subHours(2)->timestamp => [
                'ident' => 5,
                'text'  => '提前2小时',
            ],
            Carbon::parse($startTime, $this->timeZone)->subDay()->timestamp => [
                'ident' => 6,
                'text'  => '提前1天',
            ],
            Carbon::parse($startTime, $this->timeZone)->subDays(2)->timestamp => [
                'ident' => 7,
                'text'  => '提前2天',
            ],
            Carbon::parse($startTime, $this->timeZone)->subWeek()->timestamp => [
                'ident' => 8,
                'text'  => '提前1周',
            ],
            default => [
                'ident' => 0,
                'text'  => '任务开始时',
            ]
        };
    }

    /**
     * 两个时间段是否交集.
     * @param mixed $start1
     * @param mixed $end1
     * @param mixed $start2
     * @param mixed $end2
     */
    private function hasOverlap($start1, $end1, $start2, $end2): bool
    {
        if ($end1 < $start2 || $start1 > $end2) {
            // 两个时间段没有交集
            return false;
        }
        // 两个时间段有交集
        return true;
    }

    private function deleteSystemSchedule(array $info)
    {
        if (! $this->remindDao->exists(['uniqued' => $info['remind_data']['uniqued']])) {
            switch ($info['cid']) {
                case ScheduleEnum::TYPE_CLIENT_TRACK:
                    $follow = app()->get(FollowUpService::class);
                    $follow->delScheduleAfter($info['remind_data']['uniqued']);
                    break;
                case ScheduleEnum::TYPE_CLIENT_RENEW:
                case ScheduleEnum::TYPE_CLIENT_RETURN:
                    $remind = app()->get(RemindService::class);
                    $remind->delScheduleAfter($info['remind_data']['uniqued']);
                    break;
                case ScheduleEnum::TYPE_PERSONAL:
                    Task::deliver(new StatusChangeTask(ScheduleEnum::SCHEDULE_FOLLOW_NOTICE, CommonEnum::STATUS_DELETE, $info['remind_data']['entid'], $info['id']));
                    break;
            }
        }
    }

    /**
     * 更新消息状态
     * @throws BindingResolutionException
     */
    private function changeNoticeStatus(int $entId, array $info, string $startTime = '', string $endTime = '', bool $withAfter = false)
    {
        [$linkId, $type] = match ((int) $info['cid']) {
            2 => [$info['link_id'], NoticeEnum::DEALT_CLIENT_WORK_TYPE],
            3, 4 => [$info['link_id'], NoticeEnum::DEALT_MONEY_WORK_TYPE],
            default => [$info['id'], NoticeEnum::DEALT_PRESON_WORK_TYPE],
        };

        $where = ['entid' => $entId, 'template_type' => $type, 'link_status' => 0, 'link_id' => $linkId];
        app()->get(NoticeRecordService::class)->select($where, ['id', 'other'])->each(function ($item) use ($startTime, $endTime, $withAfter) {
            $other = is_string($item->other) ? json_decode($item->other, true) : $item->other;

            if (! isset($other['start_time']) && ! isset($other['end_time'])) {
                $item->link_status = CommonEnum::STATUS_DELETE;
                $item->save();
            } else {
                $recordStartTime = $other['start_time'] ?? '';
                $recordEndTime   = $other['end_time'] ?? '';
                if (($withAfter && $recordStartTime >= $startTime && $recordEndTime >= $endTime) || ($recordStartTime == $startTime && $recordEndTime == $endTime)) {
                    $item->link_status = CommonEnum::STATUS_DELETE;
                    $item->save();
                }
            }
        });
    }
}
