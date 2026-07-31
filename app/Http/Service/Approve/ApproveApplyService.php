<?php

declare(strict_types=1);


namespace App\Http\Service\Approve;

use App\Constants\ApproveEnum;
use App\Constants\CacheEnum;
use App\Http\Dao\Approve\ApproveApplyDao;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\Crud\SystemCrudApproveProcessService;
use App\Http\Service\Crud\SystemCrudApproveRecordService;
use App\Http\Service\Crud\SystemCrudApproveRuleService;
use App\Http\Service\Crud\SystemCrudApproveService;
use App\Http\Service\Frame\FrameAssistService;
use App\Http\Service\Frame\FrameService;
use App\Task\approve\ApplySavedTask;
use App\Task\approve\ApprovedTask;
use App\Task\approve\ApproveRejectTask;
use App\Task\approve\ApproveRevokeTask;
use App\Task\message\BusinessAdoptApplyRemind;
use App\Task\message\BusinessAdoptCcRemind;
use App\Task\message\BusinessApprovalRemind;
use App\Task\message\BusinessFailRemind;
use App\Task\message\BusinessRecallRemind;
use crmeb\basic\BaseService;
use crmeb\traits\service\ResourceServiceTrait;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Webpatser\Uuid\Uuid;

/**
 * 申请记录表.
 * @mixin ApproveApplyDao
 */
class ApproveApplyService extends BaseService
{
    use ResourceServiceTrait;

    protected $page = 1;

    protected $limit = 0;

    public function __construct(ApproveApplyDao $dao)
    {
        $this->dao = $dao;
    }

    public function setLimit($page, $limit)
    {
        $this->page  = $page;
        $this->limit = $limit;
        return $this;
    }

    /**
     * 列表.
     * @param array|string[] $field
     * @param string $sort
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getList(array $where, array $field = ['*'], $sort = 'id', array $with = []): array
    {
        $userId = auth('admin')->id();
        $where  = $this->getWhere($where, $userId);
        // 分页参数
        [$page, $limit] = $this->page && $this->limit ? [$this->page, $this->limit] : $this->getPageValue();
        // 获取服务实例（使用集合统一管理）
        $services = collect([
            'approveUser' => app()->get(ApproveUserService::class),
            'frame'       => app()->get(FrameService::class),
            'content'     => app()->get(ApproveContentService::class),
            'approve'     => app()->get(ApproveService::class),
            'crudApprove' => app()->get(SystemCrudApproveService::class),
        ]);
        // 查询列表并处理列表数据
        $list = collect($this->dao->getApplyList($where, $field, $page, $limit, $sort, [
            'card',
            'rules',
            'crud_rules',
            'recall' => fn ($q) => $q->select(['id as recall_id', 'apply_id']),
        ]))->map(function ($item) use ($services, $userId) {
            try {
                if ($item['is_recall'] || ! $item['crud_id']) {
                    $item['approve'] = $services['approve']->setTrashed()->get($item['approve_id'], ['id', 'name', 'icon', 'color', 'info', 'types']);
                    $item['content'] = $services['content']->getContent($item['id'], ['uploadFrom']);
                } else {
                    $item['approve'] = $services['crudApprove']->setTrashed()->get($item['approve_id'], ['id', 'name', 'icon', 'color', 'info', 'types']);
                    $item['content'] = $services['crudApprove']->getContent($item['crud_id'], $item['link_id'], $item['id']);
                }
                // 处理审核状态
                $item['verify_status'] = $item['status'] == -1 ? -1 : $services['approveUser']->getVerifyStatus($userId, $item['node_id'], $item['id']);
                // 处理信息和规则
                $item['frame'] = $services['frame']->getMasterFrame($item['user_id'], ['id', 'name']);
                $item['rules'] = $item['crud_id'] ? $item['crud_rules'] : $item['rules'];
                return $item;
            } catch (\Exception) {
                return [];
            }
        })->filter()->values()->toArray();
        $count = $this->dao->count($where);
        return $this->listData($list, $count);
    }

    /**
     * 获取绩效导出列表.
     * @param mixed $where
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getListForExport($where): array
    {
        $where          = $this->getWhere($where, auth('admin')->id());
        $contentService = app()->get(ApproveContentService::class);
        $list           = $this->dao->setDefaultSort('created_at')->select($where, with: [
            'approve' => fn ($q) => $q->select(['id', 'name']),
            'card'    => fn ($q) => $q->with(['frame']),
        ])->each(function ($item) use ($contentService) {
            $item['content'] = $contentService->getContent($item['id'])->toArray();
        })?->toArray();
        $exports = $title = [];
        if (! empty($list)) {
            $title = ['审批编号', '审批类型', '申请人', '部门'];
            foreach ($list as $key => $item) {
                $export = [
                    $item['number'],
                    ! empty($item['approve']) ? $item['approve']['name'] : '未知',
                    ! empty($item['card']) ? $item['card']['name'] : '未知',
                    ! empty($item['card']['frame']) ? $item['card']['frame']['name'] : '未知',
                ];
                if ($item['content']) {
                    $index = 1;
                    collect($item['content'])->each(function ($value) use (&$title, &$export, &$index, $key) {
                        if (is_object($value['value'])) {
                            $value['value']->flatten(1)->each(function ($ite) use (&$title, &$export, $value, &$index, $key) {
                                if ($key == 0) {
                                    $tit = $value['label'] . $index . '.' . $ite['label'];
                                    if (in_array($tit, $title)) {
                                        ++$index;
                                        $tit = $value['label'] . $index . '.' . $ite['label'];
                                    }
                                    $title[] = $tit;
                                }
                                $export[] = $ite['value'];
                            });
                        } elseif (is_array($value['value'])) {
                            collect($value['value'])->each(function ($ite) use (&$title, &$export, $key, $value) {
                                if ($key == 0) {
                                    $title[] = $ite['label'] ?? $value['label'];
                                }
                                $export[] = $ite['value'] ?? ($ite['url'] ?? '');
                            });
                        } else {
                            if ($key == 0) {
                                $title[] = $value['label'];
                            }
                            $export[] = $value['value'];
                        }
                    });
                }
                if ($key == 0) {
                    $title[] = '审批状态';
                }
                $export[] = match ($item['status']) {
                    -1      => '撤回',
                    1       => '已通过',
                    2       => '已拒绝',
                    default => '待审批',
                };
                if ($key == 0) {
                    $title[] = '申请时间';
                }
                $export[]  = $item['created_at'];
                $exports[] = $export;
            }
        }
        return compact('title', 'exports');
    }

    /**
     * 获取详情.
     * @return string
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function resourceEdit(int $id, array $other = [])
    {
        $cacheKey = md5(json_encode($other) . $id);
        $cacheTtl = (int) sys_config('system_cache_ttl', 3600);
        $result   = Cache::tags([CacheEnum::TAG_APPROVE])->remember($cacheKey, $cacheTtl, function () use ($id, $other) {
            if (! $this->dao->exists(['id' => $id])) {
                return json_encode(['users' => [], 'reply' => []], JSON_UNESCAPED_UNICODE);
            }
            $userService        = app(ApproveUserService::class);
            $replyService       = app(ApproveReplyService::class);
            $crudApproveService = app()->get(SystemCrudApproveService::class);
            if (! empty($other['types'])) {
                // 获取基础信息（关联查询条件单独提取）
                $info = $this->dao->get(['id' => $id], ['*'], [
                    'card'        => fn ($query) => $query->select(['id', 'name', 'avatar', 'uid']),
                    'approve'     => fn ($query) => $query->select(['id', 'name', 'icon', 'color', 'info', 'types']),
                    'crudApprove' => fn ($query) => $query->select(['id', 'name', 'icon', 'color', 'info', 'types']),
                    'rules', 'frame',
                    'crud_rules',
                    'recall' => fn ($q) => $q->select(['id as recall_id', 'apply_id']),
                ])?->toArray();
                if (empty($info)) {
                    return json_encode([]);
                }
                $info['approve'] = $info['crud_id'] ? $info['crud_approve'] : $info['approve'];
                unset($info['crud_approve']);
                if ($info['is_recall'] || ! $info['crud_id']) {
                    $info['content'] = app()->get(ApproveContentService::class)->getContent($id);
                } else {
                    $info['content'] = $crudApproveService->getContent($info['crud_id'], $info['link_id'], $id);
                }
                // 处理审批节点数据（变量提前定义，避免重复查询）
                $uniques = $userService->getUniques($id);
                $users   = [];
                foreach ($uniques as $v) {
                    $process = $userService->value(['node_id' => $v, 'apply_id' => $id], 'process_info');
                    $title   = $process['name'] ?? '';
                    // 用户列表查询条件合并
                    $userList = $userService->getUserList(
                        ['node_id' => $v, 'apply_id' => $id],
                        ['status', 'updated_at', 'user_id', 'is_sign', 'is_transfer', 'content', 'parent'],
                        ['level' => 'asc', 'sort' => 'asc', 'id' => 'asc'],
                        ['card'  => fn ($query) => $query->select(['id', 'name', 'avatar', 'uid'])]
                    )?->toArray() ?? [];
                    $users[] = [
                        'uniqued'      => $v,
                        'apply_id'     => $id,
                        'types'        => $process['types'],
                        'title'        => $title,
                        'examine_mode' => $process['examine_mode'],
                        'updated_at'   => $userService->getValue(
                            ['node_id' => $v, 'apply_id' => $id, 'status' => 1],
                            'updated_at',
                            ['updated_at' => 'desc']
                        ) ?: '',
                        'users' => $userList,
                    ];
                }
                $info['reply'] = collect($replyService->dao->setDefaultSort('id')->select(
                    ['apply_id' => $id],
                    ['id', 'user_id', 'apply_id', 'content', 'created_at'],
                    [
                        'card'  => fn ($query) => $query->select(['id', 'name', 'avatar', 'uid']),
                        'files' => fn ($query) => $query->select(['id', 'att_dir as src', 'att_size as size', 'relation_id', 'real_name as name']),
                    ]
                )?->toArray())
                    ->map(function ($item) {
                        $item['files'] = collect($item['files'] ?? [])->map(fn ($file) => array_merge($file, ['src' => $file['src'] ? link_file($file['src']) : '']))->all();
                        return $item;
                    })->all();
                $info['verify_status'] = $info['status'] == -1
                    ? -1
                    : $userService->getVerifyStatus(auth('admin')->id(), $info['node_id'], $id);
                $info['users'] = $users;
                $info['rules'] = $info['crud_id'] ? $info['crud_rules'] : $info['rules'];
            } else {
                $info = $this->dao->get(['id' => $id], ['*'], [
                    'content' => fn ($query) => $query->select(['apply_id', 'uniqued', 'value', 'content', 'options', 'symbol']),
                    'card'    => fn ($query) => $query->select(['id', 'name', 'avatar', 'uid']),
                ])?->toArray() ?? [];
                if (! empty($info['crud_id']) && empty($info['content'])) {
                    $info['content'] = $crudApproveService->getContent(
                        $info['crud_id'],
                        $info['link_id'],
                        $info['approve_id']
                    );
                }
                $content = [];
                foreach ($info['content'] ?? [] as $v) {
                    $content[$v['uniqued']] = is_numeric($v['value']) ? (float) $v['value'] : $v['value'];
                }
                $info = $content;
            }
            return json_encode($info, JSON_UNESCAPED_UNICODE);
        });
        return json_decode($result, true);
    }

    /**
     * 保存审批申请.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function saveForm(array $form, array $process, int $approveId, int $applyId = 0, int $userId = 0, int $entId = 1, int $linkId = 0)
    {
        $saveForm = collect(app()->get(ApproveFormService::class)->select(['approve_id' => $approveId])?->toArray() ?? [])->flatMap(function ($v) use ($form) {
            $hasValidChildren = isset($v['content']['children']) && $v['content']['type'] !== 'approvalBill';
            if ($hasValidChildren) {
                return collect($v['content']['children'])->map(function ($value) use ($v, $form) {
                    $item             = $v;
                    $item['title']    = $value['title'];
                    $item['required'] = $value['effect']['required'] ?? false;
                    $item['info']     = $value['info'] ?? '';
                    if (is_string($value)) {
                        $item['value']  = $form[$v['uniqued']] ?? null;
                        $item['symbol'] = $v['content']['symbol'] ?? '';
                    } else {
                        $item['value']   = $form[$value['field']] ?? null;
                        $item['types']   = $value['type'] ?? null;
                        $item['uniqued'] = $value['field'] ?? null;
                        $item['symbol']  = $value['symbol'] ?? '';
                        $item['content'] = $value;
                    }
                    return $item;
                });
            } else {
                $v['value']  = $form[$v['uniqued']] ?? null;
                $v['symbol'] = $v['content']['symbol'] ?? '';
                return [$v];
            }
        })->all();
        // TODO 无须审核流程
        if (! app()->get(ApproveService::class)->value($approveId, 'examine')) {
            $res = $this->transaction(function () use ($entId, $approveId, $saveForm, $userId, $linkId) {
                $newId = $this->dao->create([
                    'entid'      => $entId,
                    'user_id'    => $userId,
                    'approve_id' => $approveId,
                    'link_id'    => $linkId,
                    'number'     => $entId . $approveId . substr((string) time(), 4, 6),
                    'node_id'    => '',
                    'status'     => 1,
                    'examine'    => 0,
                ])->id;
                $res1 = app()->get(ApproveContentService::class)->saveMore($saveForm, $newId);
                if (! $res1) {
                    throw $this->exception('保存失败');
                }
                return $newId;
            });
        } else {
            if (! $process) {
                $form    = app()->get(ApproveProcessService::class)->verifyForm($form, $approveId, $userId);
                $process = $form['list'];
            }
            $count = count($process);

            $empty = 0;
            foreach ($process as $val) {
                if (! $val['users']) {
                    ++$empty;
                }
            }
            if ($empty == $count) {
                throw $this->exception('流程节点至少存在一个人员');
            }
            $res = $this->transaction(function () use ($saveForm, $process, $approveId, $applyId, $entId, $userId, $linkId) {
                if ($applyId && $this->dao->value(['id' => $applyId], 'status') != 1) {
                    $this->dao->update(['id' => $applyId], ['status' => -1]);
                }
                $newId = $this->dao->create([
                    'entid'      => $entId,
                    'user_id'    => $userId,
                    'approve_id' => $approveId,
                    'link_id'    => $linkId,
                    'number'     => $entId . $approveId . substr((string) time(), 4, 6),
                    'node_id'    => $process[0]['uniqued'],
                    'status'     => 0,
                ])->id;
                $res1 = app()->get(ApproveContentService::class)->saveMore($saveForm, $newId);
                $res2 = app()->get(ApproveUserService::class)->saveMore($process, $approveId, $newId);
                if (! $res1 || ! $res2) {
                    throw $this->exception('保存失败');
                }
                return $newId;
            });
            // 自动走抄送流程
            event('approve.autoCopy', [$process, $res, $entId, app()->get(ApproveUserService::class)]);
            // 提醒下个审核人
            Task::deliver(new BusinessApprovalRemind($entId, (int) $res));
        }
        Task::deliver(new ApplySavedTask($res, $saveForm));
        Cache::tags([CacheEnum::TAG_APPROVE])->flush();
        return $res;
    }

    /**
     * 保存实体审批.
     * @param mixed $crudId
     * @param mixed $data
     * @param mixed $approveId
     * @param mixed $userId
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function saveCrudApply($crudId, $data, $approveId, $userId, int $entId = 1, int $linkId = 0, string $action = '', string $table = '', array $scheduleData = [], array $originalData = [], array $originalScheduleData = [])
    {
        $process = app()->get(SystemCrudApproveProcessService::class)->verifyForm($data, $approveId, $userId);
        if (! $process) {
            throw $this->exception('缺少审批流程');
        }
        $count = count($process);
        $empty = 0;
        foreach ($process as $val) {
            if (! $val['users']) {
                ++$empty;
            }
        }
        if ($empty == $count) {
            throw $this->exception('流程节点至少存在一个人员');
        }
        $res = $this->transaction(function () use ($crudId, $process, $approveId, $entId, $userId, $linkId) {
            $newId = $this->dao->getIncId([
                'entid'      => $entId,
                'crud_id'    => $crudId,
                'link_id'    => $linkId,
                'user_id'    => $userId,
                'approve_id' => $approveId,
                'number'     => $approveId . substr((string) time(), 4, 6),
                'node_id'    => $process[0]['uniqued'],
                'status'     => 0,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ]);
            $res = app()->get(ApproveUserService::class)->saveMore($process, $approveId, $newId, true);
            ! $res && throw $this->exception('保存失败');
            return $newId;
        });
        // 生成审批历史数据
        app()->get(SystemCrudApproveRecordService::class)->create([
            'crud_id'                => $crudId,
            'data_id'                => $linkId,
            'approve_id'             => $res,
            'event'                  => $action,
            'table_name'             => $table,
            'data'                   => $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : '',
            'schedule_data'          => $scheduleData ? json_encode($scheduleData, JSON_UNESCAPED_UNICODE) : '',
            'original_data'          => $originalData ? json_encode($originalData, JSON_UNESCAPED_UNICODE) : '',
            'original_schedule_data' => $originalScheduleData ? json_encode($originalScheduleData, JSON_UNESCAPED_UNICODE) : '',
        ]);
        // 自动走抄送流程
        event('approve.autoCopy', [$process, $res, $entId, app()->get(ApproveUserService::class)]);
        // 提醒下个审核人
        Task::deliver(new BusinessApprovalRemind($entId, (int) $res));
        Cache::tags([CacheEnum::TAG_APPROVE])->flush();
        return $res;
    }

    /**
     * 审批.
     * @param mixed $id
     * @param mixed $uid
     * @param mixed $status
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function verify(int $id, int $uid, int $status)
    {
        // 获取审批信息
        $applyInfo = $this->dao->get(['id' => $id], ['approve_id', 'crud_id', 'node_id', 'is_recall'])?->toArray();
        if (! $applyInfo) {
            throw $this->exception('无效的审批信息');
        }
        // 权限校验
        $userService       = app(ApproveUserService::class);
        $authorizedUserIds = collect($userService->column(['apply_id' => $id], 'user_id') ?? []);
        if (! $authorizedUserIds->contains($uid)) {
            throw $this->exception('您暂时没有操作权限');
        }
        $edit = [];
        if ($status) {
            // 获取规则信息
            $ruleService = $applyInfo['is_recall'] || ! $applyInfo['crud_id']
                ? app(ApproveRuleService::class)
                : app(SystemCrudApproveRuleService::class);
            $ruleInfo = $ruleService->get(['approve_id' => $applyInfo['approve_id']])?->toArray();
            // 处理审批用户列表
            $approveUsers = collect($userService->dao->setDefaultSort(['level' => 'asc'])->select(['apply_id' => $id])?->toArray());
            // 筛选当前用户的待处理项
            $userInfo = $approveUsers->filter(function ($item) use ($applyInfo, $uid) {
                return $item['node_id'] == $applyInfo['node_id']
                    && $item['user_id'] == $uid
                    && $item['status'] == 0;
            });
            if ($userInfo->isEmpty()) {
                return true;
            }
            // 自动审批处理
            $this->autoVerify($ruleInfo, $userService, $id, $applyInfo, $userInfo->first());
            // 刷新审批用户列表
            $approveUsers = collect($userService->dao->setDefaultSort(['level' => 'asc'])->select(['apply_id' => $id, 'not_status' => -1])?->toArray());
            // 更新当前用户审批状态
            $userService->update(
                [
                    'apply_id' => $id,
                    'user_id'  => $uid,
                    'types'    => 1,
                    'status'   => 0,
                    'node_id'  => $applyInfo['node_id'],
                ],
                ['verify' => 1, 'status' => 1]
            );
            // 更新用户列表状态
            $updatedUsers = $approveUsers->map(function ($item) use ($uid, $applyInfo) {
                if ($item['user_id'] == $uid && $item['types'] == 1 && $item['node_id'] == $applyInfo['node_id']) {
                    $item['status'] = 1;
                    $item['verify'] = 1;
                }
                return $item;
            });
            // 处理下一节点
            $examineMode = (int) $userInfo->first()['process_info']['examine_mode'];
            $passRatio   = (int) ($userInfo->first()['process_info']['pass_ratio'] ?? 0);
            // 或签模式(examine_mode=1)：pass_ratio=0 保持任意一人通过；pass_ratio>0 达到比例后节点通过。
            $edit['node_id'] = match ($examineMode) {
                1 => $this->nextNode($updatedUsers, $userService, $id, $userInfo->first()['level'], $applyInfo['node_id'], false, $passRatio),
                2, 3 => $this->nextNode($updatedUsers, $userService, $id, $userInfo->first()['level'], $applyInfo['node_id'], false),
                default => $applyInfo['node_id'],
            };
            // 处理审批状态：nextNode 会返回首个仍需处理的节点；若返回节点已满足通过条件，则流程结束。
            $nodeUsers      = collect($userService->dao->select(['apply_id' => $id, 'node_id' => $edit['node_id'], 'not_status' => -1])?->toArray());
            $edit['status'] = $this->resolveApplyStatus($nodeUsers);
        } else {
            // 拒绝审批流程
            $userService->update(
                ['apply_id' => $id, 'node_id' => $applyInfo['node_id'], 'user_id' => $uid, 'types' => 1],
                ['verify' => 1, 'status' => 2]
            );
            $edit['status'] = 2;
        }
        // 保存更新并触发事件
        $this->dao->update(['id' => $id], $edit);
        $res = $edit['node_id'] ?? true;
        Cache::tags([CacheEnum::TAG_APPROVE])->flush();
        Task::deliver(new BusinessApprovalRemind(1, $id));
        // 分发不同状态的任务
        match ($edit['status']) {
            1 => [
                Task::deliver(new ApprovedTask($id)),
                Task::deliver(new BusinessAdoptApplyRemind(1, $uid, $id)),
            ],
            2 => [
                Task::deliver(new ApproveRejectTask($id)),
                Task::deliver(new BusinessFailRemind(1, $id)),
            ],
            default => null
        };

        return $res;
    }

    /**
     * 获取下级流程节点ID.
     * @param mixed $applyId
     * @param mixed $level
     * @param mixed $node_id
     * @param mixed $approveUsers
     * @param mixed $userService
     * @param bool $isOr
     * @param int $passRatio 通过比例(%)，兼容旧调用参数，实际以节点 process_info 为准
     * @return mixed
     */
    public function nextNode(Collection $approveUsers, $userService, $applyId, $level, $node_id, bool $isOr = true, int $passRatio = 0)
    {
        $currentNodeUsers = $approveUsers->filter(function ($item) use ($node_id) {
            return $item['node_id'] == $node_id && $item['types'] == 1;
        });
        if ($currentNodeUsers->isNotEmpty() && ! $this->isApprovalNodePassed($currentNodeUsers, $passRatio)) {
            return $node_id;
        }
        // 查找下一级节点（level+1）
        $nextLevelItems = $approveUsers->filter(fn ($item) => $item['level'] == $level + 1);
        if ($nextLevelItems->isEmpty()) {
            return $node_id; // 无下一级，返回当前节点
        }
        $firstNextItem = $nextLevelItems->first();
        // 下一级为抄送人（types=2）：触发提醒并递归
        if ($firstNextItem['types'] == 2) {
            // 抄送人审批通过提醒
            Task::deliver(new BusinessAdoptCcRemind(1, (int) $applyId, $firstNextItem['node_id']));
            // 更新抄送人节点状态
            $userService->update(['apply_id' => $applyId, 'node_id' => $firstNextItem['node_id']], ['status' => 1]);
            // 更新集合中对应项状态（保持数据一致性）
            $updatedUsers = $approveUsers->map(function ($item) use ($firstNextItem) {
                return $item['node_id'] == $firstNextItem['node_id']
                    ? array_merge($item, ['status' => 1])
                    : $item;
            });
            // 递归查找下一级
            return $this->nextNode($updatedUsers, $userService, $applyId, $firstNextItem['level'], $firstNextItem['node_id']);
        }
        // 下一级为审批节点：按审批模式处理
        $nextNodeUsers = $nextLevelItems->filter(fn ($item) => $item['types'] == 1);
        if ($nextNodeUsers->isNotEmpty() && $this->isApprovalNodePassed($nextNodeUsers)) {
            return $this->nextNode($approveUsers, $userService, $applyId, $firstNextItem['level'], $firstNextItem['node_id']);
        }
        return $firstNextItem['node_id'];
    }

    /**
     * 根据当前节点审批人状态判断整条申请是否已完成.
     */
    private function resolveApplyStatus(Collection $nodeUsers): int
    {
        $approvalUsers = $nodeUsers->filter(fn ($item) => $item['types'] == 1);
        if ($approvalUsers->isEmpty()) {
            return 1;
        }
        return $this->isApprovalNodePassed($approvalUsers) ? 1 : 0;
    }

    /**
     * 判断审批节点是否满足通过条件.
     */
    private function isApprovalNodePassed(Collection $nodeUsers, int $fallbackPassRatio = 0): bool
    {
        $nodeUsers = $nodeUsers->filter(fn ($item) => $item['types'] == 1);
        if ($nodeUsers->isEmpty()) {
            return false;
        }

        $processInfo = $nodeUsers->first()['process_info'] ?? [];
        $examineMode = (int) ($processInfo['examine_mode'] ?? 0);
        if ($examineMode === 1) {
            $passRatio = (int) ($processInfo['pass_ratio'] ?? $fallbackPassRatio);
            if ($passRatio > 0) {
                return $this->checkPassRatio($nodeUsers, $passRatio);
            }
            return $nodeUsers->contains(fn ($item) => $item['status'] == 1);
        }

        return $nodeUsers->doesntContain(fn ($item) => $item['status'] == 0);
    }

    /**
     * 检查当前节点是否满足通过比例.
     * @param Collection $nodeUsers 当前节点的所有审批用户
     * @param int $passRatio 通过比例(%)，0=关闭（原逻辑：任意一人同意即通过）
     * @return bool
     */
    private function checkPassRatio(Collection $nodeUsers, int $passRatio): bool
    {
        // pass_ratio=0 表示关闭比例模式，使用原逻辑（任意一人同意即通过）
        if ($passRatio <= 0) {
            return true;
        }

        $passRatio    = min($passRatio, 100);
        $totalUsers    = $nodeUsers->count();
        if ($totalUsers <= 0) {
            return false;
        }
        $approvedUsers = $nodeUsers->filter(fn ($item) => $item['status'] == 1)->count();

        // 计算通过阈值（向上取整）
        $threshold = (int) ceil($totalUsers * $passRatio / 100);

        return $approvedUsers >= $threshold;
    }

    /**
     * 撤销申请.
     * @param mixed $id
     * @param mixed $userId
     * @return int
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function revokeApply($id, int $userId, string $content = '')
    {
        $info = $this->dao->get(['id' => $id, 'user_id' => $userId])?->toArray();
        if (! $info) {
            throw $this->exception('暂无可操作记录！');
        }
        if (! app()->get(ApproveService::class)->value(['id' => $info['approve_id']], 'examine')) {
            // 【业务类型】撤回提醒
            Task::deliver(new BusinessRecallRemind(1, $userId, $info));
            Task::deliver(new ApproveRevokeTask((int) $id));
            return $this->dao->update(['id' => $id], ['status' => -1]) && Cache::tags([CacheEnum::TAG_APPROVE])->flush();
        }
        switch ($info['status']) {
            case 1:
                $recall = $info['crud_id'] ? app()->get(SystemCrudApproveRuleService::class)->value(['approve_id' => $info['approve_id']], 'recall')
                    : app()->get(ApproveRuleService::class)->value(['approve_id' => $info['approve_id']], 'recall');
                if (! $recall) {
                    throw $this->exception('该申请不允许通过后撤销！');
                }
                if ($this->dao->exists(['apply_id' => $id, 'status' => 0])) {
                    throw $this->exception('已申请撤销中，请勿重复操作！');
                }
                $approveUserService = app()->get(ApproveUserService::class);
                $approveUsers       = $approveUserService->dao->setDefaultSort(['level' => 'asc', 'sort' => 'asc'])->select([
                    'apply_id'    => $id,
                    'is_sign'     => [0, 2],
                    'is_transfer' => [0, 2],
                ])?->toArray() ?: [];
                $applyId = $this->transaction(function () use ($approveUserService, $approveUsers, $info, $id, $content, $userId) {
                    $approve_id = app()->get(ApproveService::class)->value(['types' => ApproveEnum::APPROVE_REVOKE], 'id');
                    unset($info['id']);
                    $info['card_id']    = $info['user_id'];
                    $info['status']     = 0;
                    $info['apply_id']   = $id;
                    $info['approve_id'] = $approve_id;
                    $info['node_id']    = $approveUsers[0]['node_id'] ?? $info['node_id'];
                    $info['is_recall']  = 1;
                    $info['created_at'] = $info['updated_at'] = now()->toDateTimeString();
                    $info['number']     = substr((string) time(), 4, 6);
                    $applyId            = $this->dao->getIncId($info);
                    $formService        = app()->get(ApproveFormService::class);
                    // 审批内容-提交时间
                    $dateContent    = $formService->get(['approve_id' => $approve_id, 'types' => 'datePicker'], ['content', 'props', 'uniqued'])?->toArray();
                    $contentService = app()->get(ApproveContentService::class);
                    $contentService->create([
                        'apply_id'   => $applyId,
                        'approve_id' => $approve_id,
                        'user_id'    => $userId,
                        'card_id'    => $userId,
                        'title'      => '提交时间',
                        'value'      => now()->toDateTimeString(),
                        'types'      => 'datePicker',
                        'content'    => $dateContent['content'],
                        'props'      => $dateContent['props'],
                        'uniqued'    => $dateContent['uniqued'],
                    ]);
                    // 审批内容-撤销理由
                    $inputContent = $formService->get(['approve_id' => $approve_id, 'types' => 'input'], ['content', 'props', 'uniqued'])?->toArray();
                    $contentService->create([
                        'apply_id'   => $applyId,
                        'approve_id' => $approve_id,
                        'user_id'    => $userId,
                        'card_id'    => $userId,
                        'title'      => '撤销理由',
                        'value'      => $content,
                        'types'      => 'input',
                        'content'    => $inputContent['content'],
                        'props'      => $inputContent['props'],
                        'uniqued'    => $inputContent['uniqued'],
                    ]);
                    $uniqued     = [];
                    $level       = 1;
                    $userService = app()->get(AdminService::class);
                    foreach ($approveUsers as $item) {
                        unset($item['id']);
                        if ($uniqued && ! in_array($item['node_id'], $uniqued)) {
                            ++$level;
                        }
                        $item['level']                = $level;
                        $uniqued[]                    = $item['node_id'];
                        $item['is_sign']              = 0;
                        $item['is_transfer']          = 0;
                        $item['status']               = 0;
                        $item['content']              = '';
                        $item['apply_id']             = $applyId;
                        $item['approve_id']           = $approve_id;
                        $item['info']                 = $userService->get($item['user_id'], ['id', 'uid', 'name', 'avatar'])?->toArray() ?: [];
                        $item['process_info']['name'] = $item['types'] == 1 ? '审核人' : '抄送人';
                        $approveUserService->create($item);
                    }
                    return $applyId;
                });
                return $applyId && Task::deliver(new BusinessApprovalRemind(1, $applyId)) && Cache::tags([CacheEnum::TAG_APPROVE])->flush();
            default:
                // 【业务类型】撤回提醒
                Task::deliver(new BusinessRecallRemind(1, $userId, $info));
                Task::deliver(new ApproveRevokeTask((int) $id));
                return $this->dao->update(['id' => $id], ['status' => -1]) && Cache::tags([CacheEnum::TAG_APPROVE])->flush();
        }
    }

    /**
     * 待我审批数量.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getApproveCount(int $userId): int
    {
        $where = ['types' => 1, 'name' => '', 'verify_status' => 1];
        return $this->dao->count($this->getWhere($where, $userId));
    }

    /**
     * 审批催办.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws InvalidArgumentException
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function urge(int $id, int $uid): void
    {
        if ($this->dao->value(['id' => $id], 'status') != 0) {
            throw $this->exception('该审批信息无需催办');
        }
        if (Cache::tags([CacheEnum::TAG_APPROVE])->has(md5('urge' . $uid . $id))) {
            throw $this->exception('操作太过频繁，请稍后再试');
        }
        Task::deliver(new BusinessApprovalRemind(1, $id));
        Cache::tags([CacheEnum::TAG_APPROVE])->set(md5('urge' . $uid . $id), 1, ApproveEnum::APPROVE_URGE_INTERVAL);
    }

    /**
     * 根据类型获取审批通过条数.
     * @param mixed $time
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getApplyNumByTypes(int $uid, int $types, $time = 'month'): int
    {
        $approveIds = app()->get(ApproveService::class)->column(['types' => $types], 'id');
        return $approveIds ? $this->dao->count(['user_id' => $uid, 'approve_id' => $approveIds, 'status' => 1, 'time' => $time]) : 0;
    }

    /**
     * 审批加签.
     * @return bool
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function addSign(int $id, int $uid, array $data)
    {
        $applyInfo = $this->dao->get($id)?->toArray();
        if (! $applyInfo) {
            throw $this->exception('未找到相关审批记录');
        }
        if ($applyInfo['crud_id']) {
            $ruleInfo = app()->get(SystemCrudApproveRuleService::class)->get(['approve_id' => $applyInfo['approve_id']])?->toArray();
        } else {
            $ruleInfo = app()->get(ApproveRuleService::class)->get(['approve_id' => $applyInfo['approve_id']])?->toArray();
        }
        if (! $ruleInfo['is_sign']) {
            throw $this->exception('该审批暂不允许加签');
        }
        if ($applyInfo['status'] != 0) {
            throw $this->exception('审批已通过，无法操作');
        }
        $appUserService = app()->get(ApproveUserService::class);
        $approveUsers   = $appUserService->select(['apply_id' => $id, 'status' => 0])?->toArray() ?: [];
        $userInfo       = array_filter($approveUsers, function ($item) use ($applyInfo, $uid) {
            return $item['node_id'] == $applyInfo['node_id'] && $item['user_id'] == $uid;
        });
        if (! $userInfo) {
            throw $this->exception('暂无权限操作');
        }
        $userService = app()->get(AdminService::class);
        $node_id     = substr(str_replace('-', '', (string) Uuid::generate(4)), 0, 15);
        $level       = reset($userInfo)['level'];
        $sort        = 1;
        foreach ($data['user'] as $item) {
            $save[] = [
                'apply_id'     => $id,
                'approve_id'   => $applyInfo['approve_id'],
                'user_id'      => $item,
                'card_id'      => $item,
                'node_id'      => $node_id,
                'level'        => $level + 1,
                'types'        => 1,
                'info'         => $userService->get($item, ['id', 'uid', 'name', 'avatar'])?->toArray() ?: [],
                'process_info' => [
                    'name'           => '加签审核人',
                    'types'          => 1,
                    'settype'        => 1,
                    'director_order' => -1,
                    'director_level' => 0,
                    'no_hander'      => 0,
                    'dep_head'       => '',
                    'self_select'    => 0,
                    'select_range'   => 0,
                    'select_mode'    => 0,
                    'examine_mode'   => $data['examine_mode'],
                ],
                'is_sign' => 2,
                'sort'    => $data['examine_mode'] == 3 ? $sort : 1,
            ];
            ++$sort;
        }
        if ($data['types']) {// 在我之前加签
            $oldNode = array_filter($approveUsers, function ($item) use ($applyInfo) {
                return $item['node_id'] == $applyInfo['node_id'] && $item['is_transfer'] != 1;
            });
            array_walk($oldNode, function (&$item) {
                $item['level'] = $item['level'] + 2;
            });
            $res = $this->transaction(function () use ($appUserService, $oldNode, $save, $level, $id, $node_id, $userInfo, $data) {
                $appUserService->update(reset($userInfo)['id'], ['status' => 1, 'content' => $data['info'], 'is_sign' => 1]);
                $appUserService->inc(['level_gt' => $level], 2, 'level');
                foreach ($save as $value) {
                    $appUserService->create($value);
                }
                $oldNodeId = substr(str_replace('-', '', (string) Uuid::generate(4)), 0, 17);
                foreach ($oldNode as $val) {
                    unset($val['id']);
                    $val['is_transfer']          = 0;
                    $val['node_id']              = $oldNodeId;
                    $val['process_info']['name'] = '审核人';
                    $appUserService->create($val);
                }
                $this->dao->update($id, ['node_id' => $node_id]);
                return true;
            });
        } else {// 在我之后加签
            $oldNode = array_filter($approveUsers, function ($item) use ($applyInfo, $uid) {
                return $item['node_id'] == $applyInfo['node_id'] && $item['process_info']['examine_mode'] != 1 && $item['status'] == 0 && $item['user_id'] != $uid;
            });
            $res = $this->transaction(function () use ($appUserService, $oldNode, $save, $level, $id, $node_id, $userInfo, $data) {
                if (! $oldNode) {
                    $this->dao->update($id, ['node_id' => $node_id]);
                }
                $appUserService->update(reset($userInfo)['id'], ['status' => 1, 'content' => $data['info'], 'is_sign' => 1]);
                $appUserService->inc(['level_gt' => $level], 1, 'level');
                foreach ($save as $value) {
                    $appUserService->create($value);
                }
                return true;
            });
            $this->autoVerify($ruleInfo, $appUserService, $id, $applyInfo, reset($userInfo));
        }
        return $res && Cache::tags([CacheEnum::TAG_APPROVE])->flush() && Task::deliver(new BusinessApprovalRemind(1, $id));
    }

    /**
     * 审批转审.
     * @return bool
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function addTransfer(int $id, int $uid, array $data)
    {
        $applyInfo = $this->dao->get($id)?->toArray();
        if (! $applyInfo) {
            throw $this->exception('未找到相关审批记录');
        }
        if ($applyInfo['status'] != 0) {
            throw $this->exception('审批已通过，无法操作');
        }
        $appUserService = app()->get(ApproveUserService::class);
        $approveUsers   = $appUserService->select(['apply_id' => $id, 'not_status' => -1])?->toArray() ?: [];
        $userInfo       = array_filter($approveUsers, function ($item) use ($applyInfo, $uid) {
            return $item['node_id'] == $applyInfo['node_id'] && $item['user_id'] == $uid;
        });
        if (! $userInfo) {
            throw $this->exception('暂无权限操作');
        }
        $user = reset($userInfo);
        if ($user['status']) {
            throw $this->exception('暂无权限操作');
        }
        $nodeUsers = array_filter($approveUsers, function ($item) use ($applyInfo) {
            return $item['node_id'] == $applyInfo['node_id'];
        });
        if (array_intersect(array_column($nodeUsers, 'user_id'), $data['user'])) {
            throw $this->exception('转审用户已在审批人中');
        }
        $userService         = app()->get(AdminService::class);
        $edit                = $user;
        $edit['content']     = $data['info'];
        $edit['is_transfer'] = $user['is_transfer'] ? $user['is_transfer'] + 1 : 1;
        $edit['status']      = -1;
        unset($user['id']);
        $save = [];
        foreach ($data['user'] as $item) {
            $user['is_transfer'] = 2;
            $user['status']      = 0;
            $user['user_id']     = $item;
            $user['card_id']     = $item;
            $user['info']        = $userService->get($item, ['id', 'uid', 'name', 'avatar'])?->toArray() ?: [];
            $user['parent']      = $uid;
            $save[]              = $user;
        }
        $res = $this->transaction(function () use ($edit, $save, $appUserService) {
            $appUserService->update($edit['id'], $edit);
            foreach ($save as $value) {
                $appUserService->create($value);
            }
            return true;
        });
        return $res && Cache::tags([CacheEnum::TAG_APPROVE])->flush() && Task::deliver(new BusinessApprovalRemind(1, $id));
    }

    /**
     * 处理where条件.
     * @param mixed $where
     * @return array|mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getWhere(array $where, int $userId)
    {
        $approveUser  = app()->get(ApproveUserService::class);
        $adminService = app()->get(AdminService::class);
        return Cache::tags([CacheEnum::TAG_APPROVE])->remember(
            md5(json_encode($where) . $userId),
            (int) sys_config('system_cache_ttl', 3600),
            function () use ($where, $approveUser, $adminService, $userId) {
                switch ($where['types']) {
                    case 2:
                        if ($where['name']) {
                            $where['user_id'] = $adminService->column(['name' => $where['name']], 'id');
                        }
                        if ($where['frame_id']) {
                            $user_id = app()->get(FrameAssistService::class)->column(['frame_id' => $where['frame_id'], 'is_mastart' => 1], 'user_id');
                            if (isset($where['user_id'])) {
                                $where['user_id'] = array_intersect($adminService->column(['id' => $user_id], 'id'), $where['user_id']);
                            } else {
                                $where['user_id'] = $adminService->column(['id' => $user_id], 'id');
                            }
                        }
                        break;
                    case 1:
                        if ($where['name']) {
                            $where['user_id'] = $adminService->column(['name' => $where['name']], 'id');
                        }
                        switch ($where['verify_status']) {
                            case 4:// 已撤销
                                $ids1            = $approveUser->column(['user_id' => $userId, 'types' => 1, 'status' => [-1, 1, 2]], 'apply_id');
                                $ids2            = $this->getNowApplyId($userId);
                                $where['id']     = array_unique(array_merge($ids1, $ids2));
                                $where['status'] = -1;
                                break;
                            case 3:// 抄送我的
                                $where['id']         = $approveUser->column(['user_id' => $userId, 'types' => 2, 'status' => 1], 'apply_id');
                                $where['not_status'] = -1;
                                break;
                            case 2:// 已处理
                                $where['id']         = $approveUser->column(['user_id' => $userId, 'types' => 1, 'status' => [-1, 1, 2]], 'apply_id');
                                $where['not_status'] = -1;
                                break;
                            case 1:// 待审核
                                $where['id']     = $this->getNowApplyId($userId);
                                $where['status'] = 0;
                                break;
                            default:// 全部
                                $ids1        = $approveUser->column(['user_id' => $userId, 'types' => 2, 'status' => 1], 'apply_id');
                                $ids2        = $approveUser->column(['user_id' => $userId, 'types' => 1, 'status' => [-1, 1, 2]], 'apply_id');
                                $ids3        = $this->getNowApplyId($userId);
                                $where['id'] = array_unique(array_merge($ids1, $ids2, $ids3));
                        }
                        break;
                    default:
                        if ($where['verify_status'] == 5) {
                            $where['not_status'] = -1;
                        }
                        $where['user_id'] = $userId;
                }
                unset($where['frame_id'], $where['types'], $where['name'], $where['verify_status']);
                return $where;
            }
        );
    }

    /**
     * 自动审批.
     * @param mixed $ruleInfo
     * @param mixed $approveUserService
     * @param mixed $id
     * @param mixed $applyInfo
     * @param mixed $userInfo
     */
    protected function autoVerify($ruleInfo, $approveUserService, $id, $applyInfo, $userInfo): void
    {
        // 自动审批：0、首个节点处理，其他自动同意；1、连续审批自动同意；2、每个节点都需审批；
        switch ($ruleInfo['auto']) {
            case 0:
                $approveUserService->update(['apply_id' => $id, 'user_id' => $userInfo['user_id'], 'types' => 1, 'status' => 0, 'is_sign' => 0], ['verify' => 1, 'status' => 1]);
                break;
            case 1:
                $this->isAuto($approveUserService, $id, $userInfo['level'], $applyInfo['node_id'], uid: $userInfo['user_id']);
                break;
            case 2:
                $approveUserService->update(['apply_id' => $id, 'user_id' => $userInfo['user_id'], 'types' => 1, 'node_id' => $applyInfo['node_id']], ['verify' => 1, 'status' => 1]);
                break;
        }
    }

    protected function isAuto($approveUserService, $id, $level, $node_id, $first = true, $uid = ''): void
    {
        if ($first) {
            $approveUserService->update(['apply_id' => $id, 'user_id' => $uid, 'types' => 1, 'node_id' => $node_id], ['verify' => 1, 'status' => 1]);
        } else {
            $approveUserService->update(['apply_id' => $id, 'user_id' => $uid, 'types' => 1, 'node_id' => $node_id], ['verify' => 1]);
        }
        ++$level;
        if ($nextNodeId = $approveUserService->value(['apply_id' => $id, 'user_id' => $uid, 'types' => 1, 'level' => $level], 'node_id')) {
            $this->isAuto($approveUserService, $id, $level, $nextNodeId, false, $uid);
        }
    }

    protected function checkType($setType): string
    {
        return match ($setType) {
            1       => '指定成员审批',
            2       => '指定部门主管',
            7       => '连续多级审批',
            5       => '申请人自己审批',
            4       => '申请人自选',
            default => '无效类型',
        };
    }

    /**
     * 获取当前审批ID.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    protected function getNowApplyId(int $userId)
    {
        return Cache::tags([CacheEnum::TAG_APPROVE])->remember(md5($userId . '_approve_apply_verify_ids'), (int) sys_config('system_cache_ttl', 3600), function () use ($userId) {
            $userServices = app()->get(ApproveUserService::class);
            $apply        = $userServices->select(['user_id' => $userId, 'types' => 1, 'status' => 0], ['apply_id', 'node_id', 'sort'])?->toArray();
            if (! $apply) {
                return [];
            }
            $applyIds = [];
            foreach ($apply as $value) {
                if ($value['sort'] > 1) {
                    if ($userServices->exists(['apply_id' => $value['apply_id'], 'node_id' => $value['node_id'], 'sort' => $value['sort'] - 1, 'status' => 1])) {
                        $applyIds[] = $value['apply_id'];
                    }
                } elseif ($this->dao->exists(['id' => $value['apply_id'], 'node_id' => $value['node_id']])) {
                    $applyIds[] = $value['apply_id'];
                }
            }
            return $applyIds;
        });
    }
}
