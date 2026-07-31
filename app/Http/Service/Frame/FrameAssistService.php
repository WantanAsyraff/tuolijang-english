<?php

declare(strict_types=1);


namespace App\Http\Service\Frame;

use App\Constants\CacheEnum;
use App\Http\Dao\Frame\FrameAssistDao;
use App\Http\Service\Admin\AdminInfoService;
use App\Http\Service\Admin\AdminService;
use App\Http\Service\System\RolesService;
use App\Http\Service\System\RoleUserService;
use App\Task\frame\FrameCensusTask;
use crmeb\basic\BaseService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @mixin FrameAssistDao
 */
class FrameAssistService extends BaseService
{
    /**
     * 全部.
     */
    protected string $all = 'all';

    /**
     * 仅本人.
     */
    protected string $self = 'self';

    /**
     * 本部门(含无限下级).
     */
    protected string $department = 'dep';

    /**
     * 直属下级.
     */
    protected string $subordinate = 'sub';

    /**
     * 本人+直属下级.
     */
    protected string $team = 'team';

    public function __construct(FrameAssistDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * 批量添加.
     * @param mixed $frameIds
     * @param mixed $superUid
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function batchAdd($frameIds, array $userIds, int $masterId, int $entId = 1, int $isAdmin = 0, $superUid = 0): bool
    {
        if (! is_array($frameIds)) {
            $frameIds = [$frameIds];
        }
        $data   = [];
        $update = null;
        foreach ($frameIds as $frameId) {
            foreach ($userIds as $userId) {
                $isMaster = $masterId == $frameId;
                if ($this->dao->exists(['frame_id' => $frameId, 'user_id' => $userId, 'entid' => $entId])) {
                    if ($isMaster) {
                        $update = [
                            'frame_id'   => $frameId,
                            'user_id'    => $userId,
                            'entid'      => $entId,
                            'is_mastart' => 1,
                        ];
                    }
                    continue;
                }
                $data[] = [
                    'entid'        => $entId,
                    'frame_id'     => $frameId,
                    'user_id'      => $userId,
                    'is_mastart'   => $isMaster ? 1 : 0,
                    'is_admin'     => $isMaster ? $isAdmin : 0,
                    'superior_uid' => $isAdmin && $isMaster ? $superUid : 0,
                    'created_at'   => now()->toDateTimeString(),
                ];
            }
        }
        if ($update) {
            // 修改用户部门主管
            $this->dao->update($update, ['is_admin' => $isAdmin]);
        }
        return $this->dao->insert($data);
    }

    /**
     * 设置用户所在部门.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function setUserFrame(array|int $frameIds, int $userId, int $masterId, bool $isAdmin = false, int $superUid = 0, array $manageFrameIds = []): bool
    {
        if (! is_array($frameIds)) {
            $frameIds = [$frameIds];
        }
        $this->dao->forceDelete(['not_frame_id' => $frameIds, 'user_id' => $userId]);
        $this->dao->restore(['frame_id' => $frameIds, 'user_id' => $userId]);
        $res = $this->transaction(function () use ($frameIds, $userId, $masterId, $isAdmin, $superUid, $manageFrameIds) {
            $isAdmin && $this->dao->update(['frame_id' => $manageFrameIds, 'is_admin' => 1], ['is_admin' => 0]);
            foreach ($frameIds as $frameId) {
                $this->dao->updateOrCreate(['frame_id' => $frameId, 'user_id' => $userId], [
                    'frame_id'     => $frameId,
                    'user_id'      => $userId,
                    'is_mastart'   => $frameId == $masterId ? 1 : 0,
                    'is_admin'     => in_array($frameId, $manageFrameIds) ? (int) $isAdmin : 0,
                    'superior_uid' => $superUid ?: 0,
                ]);
            }
            return true;
        });
        return $res && Task::deliver(new FrameCensusTask());
    }

    /**
     * 角色添加部门权限.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function addFrameRole(int $id, int $masterId, int $entId = 1): void
    {
        $frame = app()->get(FrameService::class)->get($masterId, ['id', 'role_id']);
        if (! $frame) {
            throw $this->exception('主部门不存在');
        }
        if ($frame->role_id) {
            $roles = app()->get(RoleUserService::class)->column(['user_id' => $id, 'entid' => $entId], 'role_id');
            if (in_array($frame->role_id, $roles)) {
                return;
            }
            app()->get(RolesService::class)->changeUserRole($entId, $id, array_unique(array_merge($roles, [$frame->role_id])));
        }
    }

    /**
     * 获取某个部门下的所有成员id.
     * @return array
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function frameIdByUserId(array $frameIds, int $entid)
    {
        $userIds = [];
        foreach ($frameIds as $frameId) {
            $userIds = array_merge($userIds, $this->dao->getFrameUserIds($frameId, $entid));
        }
        return array_merge(array_unique($userIds));
    }

    /**
     * 根据部门IDs获取用户IDs（用于数据权限）.
     * @param array $frameIds 部门IDs
     * @param bool $includeResigned 是否包含离职人员
     * @return array
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getUserIdsByFrameIds(array $frameIds, bool $includeResigned = false): array
    {
        if (empty($frameIds)) {
            return [];
        }

        $entid = (int) (request()->hasMacro('entId') ? request()->entId() : session('ent_id', 1));
        $userIds = [];
        foreach ($frameIds as $frameId) {
            $userIds = array_merge($userIds, $this->dao->getFrameUserIds($frameId, $entid));
        }
        $userIds = array_unique(array_filter($userIds));

        // 如果不包含离职人员，需要过滤掉离职用户
        if (! $includeResigned && ! empty($userIds)) {
            // 在职人员 type: 1,2,3; 离职人员 type: 4
            $activeTypes = [1, 2, 3];
            $activeUserIds = app(AdminInfoService::class)->column(
                ['id' => $userIds, 'type' => $activeTypes],
                'id'
            );
            $userIds = array_values(array_intersect($userIds, $activeUserIds));
        }

        return array_values($userIds);
    }

    /**
     * 查找当前部门下的所有人数.
     * @param int $frameId 部门ID
     * @param int $entid 企业ID
     * @param mixed $status 状态
     * @param bool $includeResigned 是否包含离职人员
     * @return int
     * @throws \ReflectionException
     * @throws BindingResolutionException
     */
    public function getUserCount(int $frameId, int $entid = 1, $status = 1, bool $includeResigned = false)
    {
        $frameIds   = app()->get(FrameService::class)->setEntValue($entid)->scopeFrames($frameId);
        $frameIds[] = $frameId;
        $query      = ['entid' => $entid, 'frame_ids' => $frameIds];
        // 根据是否包含离职人员设置类型过滤
        if ($includeResigned) {
            $query['card'] = fn ($q) => $q->whereIn('type', [1, 2, 3, 4])->select(['id', 'type']);
        } else {
            $query['card'] = fn ($q) => $q->whereIn('type', [1, 2, 3])->select(['id', 'type']);
        }
        $uids = $this->dao->select($query, ['*'], [
            'card' => fn ($q) => $q->select(['id', 'type']),
        ])->map(function ($item) {
            return ! is_null($item->card) ? $item['user_id'] : 0;
        })->filter()->all();
        return app()->get(AdminService::class)->count(['ids' => $uids, 'status' => $status]);
    }

    /**
     * 获取用户部门.
     * @param mixed $uuid
     * @param mixed $entid
     * @return null|array|Model
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function getUserFrames($uuid, $entid = 1)
    {
        return Cache::tags([CacheEnum::TAG_FRAME])->remember(
            'user_frames' . $entid . '_' . $uuid,
            (int) sys_config('system_cache_ttl', 3600),
            function () use ($uuid) {
                $userId = app()->get(AdminService::class)->value(['uid' => $uuid], 'id');
                return $this->dao->setDefaultSort('is_mastart')->select(['user_id' => $userId], ['*'], [
                    'frame' => fn ($q) => $q->select(['id', 'name']),
                ]);
            }
        );
    }

    /**
     * 获取部门主管
     * @throws BindingResolutionException
     */
    public function getFrameAdminUserId(int $frameId): int
    {
        return intval($this->dao->value(['is_admin' => 1, 'frame_id' => $frameId], 'user_id'));
    }

    /**
     * 获取用户直属下级用户ID.
     * @throws BindingResolutionException
     * @throws \ReflectionException
     */
    public function getSubUid(int $uid, bool $normal = true): array
    {
        if ($normal) {
            $dao = $this->dao;
        } else {
            $dao = $this->dao->setTrashed();
        }
        $frameIds = $dao->column(['user_id' => $uid, 'is_admin' => 1], 'frame_id');
        if ($frameIds) {
            $uid1   = $dao->column(['frame_id' => $frameIds, 'is_mastart' => 1, 'is_admin' => 0], 'user_id');
            $notUid = $dao->column(['frame_id' => $frameIds, 'is_mastart' => 1, 'is_admin' => 1, 'not_superior_uid' => $uid], 'user_id');
            $uid1   = array_diff($uid1, $notUid);
        } else {
            $uid1 = [];
        }
        $uid2 = $dao->column(['superior_uid' => $uid], 'user_id');
        $userIds = array_unique(array_merge($uid1, $uid2));
        // 过滤在职用户（type为1、2、3）
        if ($normal) {
            $normalUserIds = app()->get(AdminInfoService::class)->column(['type' => [1, 2, 3]], 'id');
            $userIds = array_intersect($userIds, $normalUserIds);
        }
        return $userIds;
    }

    /**
     * 获取权限用户.
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getScopeUid(int $userId, array|string $scope = '', bool $normal = true): array
    {
        $result = Cache::tags([CacheEnum::TAG_FRAME, CacheEnum::TAG_ROLE])->remember(md5($userId . json_encode($scope) . (int) $normal), (int) sys_config('system_cache_ttl', 3600), function () use ($userId, $scope, $normal) {
            $roleUid = app(RolesService::class)->getDataUids(userId: $userId, normal: $normal);
            switch ($scope) {
                case $this->self:
                    $uid = [$userId];
                    break;
                case $this->department:
                    $info = $this->dao->setTrashed(! $normal)->get(['user_id' => $userId, 'is_mastart' => 1], ['frame_id', 'is_admin', 'entid']);
                    if ($info['is_admin']) {
                        $uid = app(FrameService::class)->scopeUser((int) $info['frame_id'], $normal);
                    } else {
                        $uid = $this->dao->setTrashed(! $normal)->column(['frame_id' => $info['frame_id'], 'is_mastart' => 1, 'is_admin' => 0], 'user_id');
                    }
                    $uid = array_intersect($uid, $roleUid);
                    break;
                case $this->subordinate:
                    $uid = $this->getSubUid($userId, $normal);
                    $uid = array_intersect($uid, $roleUid);
                    break;
                case $this->team:
                    $uid = array_merge($this->getSubUid($userId, $normal), [$userId]);
                    $uid = array_intersect($uid, $roleUid);
                    break;
                case $this->all:
                    $uid = $roleUid;
                    break;
                default:
                    $frameId  = app(FrameService::class)->scopeFrames($scope);
                    $frameUid = $this->dao->setTrashed(! $normal)->column(['frame_id' => $frameId, 'is_mastart' => 1], 'user_id');
                    $uid      = array_intersect($frameUid, $roleUid);
            }
            return json_encode($uid, JSON_UNESCAPED_UNICODE);
        });
        return $result ? json_decode($result, true) : [];
    }
}
