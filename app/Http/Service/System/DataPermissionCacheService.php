<?php

declare(strict_types=1);


namespace App\Http\Service\System;

use App\Constants\CacheEnum;
use App\Constants\DataPermissionLevelEnum;
use App\Constants\ModuleEnum;
use App\Http\Dao\Auth\RoleUserDao;
use App\Http\Service\Frame\FrameAssistService;
use crmeb\basic\BaseService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * 数据权限缓存服务
 * Class DataPermissionCacheService.
 */
class DataPermissionCacheService extends BaseService
{
    /**
     * 缓存前缀
     */
    protected const CACHE_PREFIX = 'data_perm';

    /**
     * 缓存TTL（秒）
     */
    protected const CACHE_TTL = 300;

    protected RoleUserDao $roleUserDao;
    protected FrameAssistService $frameAssistService;

    public function __construct(
        RoleUserDao $roleUserDao,
        FrameAssistService $frameAssistService
    ) {
        $this->roleUserDao = $roleUserDao;
        $this->frameAssistService = $frameAssistService;
    }

    /**
     * 获取缓存key
     *
     * @param int $userId 用户ID
     * @param int $roleId 角色ID
     * @param string $module 模块标识
     * @return string
     */
    protected function getCacheKey(int $userId, int $roleId, string $module): string
    {
        return sprintf('%s:%d:%d:%s', self::CACHE_PREFIX, $userId, $roleId, $module);
    }

    /**
     * 获取全局权限列表缓存key
     *
     * @param string $module 模块标识
     * @return string
     */
    protected function getGlobalCacheKey(string $module): string
    {
        return sprintf('%s:global:%s', self::CACHE_PREFIX, $module);
    }

    /**
     * 从缓存获取权限用户ID列表
     *
     * @param int $userId 用户ID
     * @param int $roleId 角色ID
     * @param string $module 模块标识
     * @return array|null 缓存未命中返回null
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getAccessibleUserIds(int $userId, int $roleId, string $module): ?array
    {
        if (!ModuleEnum::isValid($module)) {
            return null;
        }

        $cacheKey = $this->getCacheKey($userId, $roleId, $module);

        try {
            $cached = Cache::tags([CacheEnum::TAG_ROLE])->get($cacheKey);

            if ($cached !== null) {
                Log::debug('DataPermissionCache hit', [
                    'user_id' => $userId,
                    'role_id' => $roleId,
                    'module' => $module,
                    'count' => count($cached),
                ]);
                return $cached;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('DataPermissionCache get error', [
                'user_id' => $userId,
                'role_id' => $roleId,
                'module' => $module,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * 设置缓存
     *
     * @param int $userId 用户ID
     * @param int $roleId 角色ID
     * @param string $module 模块标识
     * @param array $uids 允许访问的用户ID列表
     * @return void
     */
    public function setAccessibleUserIds(int $userId, int $roleId, string $module, array $uids): void
    {
        if (!ModuleEnum::isValid($module)) {
            return;
        }

        $cacheKey = $this->getCacheKey($userId, $roleId, $module);

        try {
            Cache::tags([CacheEnum::TAG_ROLE])->put($cacheKey, $uids, self::CACHE_TTL);

            Log::debug('DataPermissionCache set', [
                'user_id' => $userId,
                'role_id' => $roleId,
                'module' => $module,
                'count' => count($uids),
                'ttl' => self::CACHE_TTL,
            ]);
        } catch (\Exception $e) {
            Log::error('DataPermissionCache set error', [
                'user_id' => $userId,
                'role_id' => $roleId,
                'module' => $module,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 清除用户所有缓存
     *
     * @param int $userId 用户ID
     * @return void
     */
    public function clearUserCache(int $userId): void
    {
        try {
            $roleIds = $this->roleUserDao->column(['user_id' => $userId, 'status' => 1], 'role_id');

            foreach (ModuleEnum::all() as $module => $name) {
                foreach ($roleIds as $roleId) {
                    $cacheKey = $this->getCacheKey($userId, $roleId, $module);
                    Cache::tags([CacheEnum::TAG_ROLE])->forget($cacheKey);
                }
            }

            Log::info('DataPermissionCache cleared for user', [
                'user_id' => $userId,
                'role_count' => count($roleIds),
            ]);
        } catch (\Exception $e) {
            Log::error('DataPermissionCache clear user error', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 清除角色所有缓存
     *
     * @param int $roleId 角色ID
     * @return void
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function clearRoleCache(int $roleId): void
    {
        try {
            $userIds = $this->roleUserDao->column(['role_id' => $roleId, 'status' => 1], 'user_id');

            foreach (ModuleEnum::all() as $module => $name) {
                foreach ($userIds as $userId) {
                    $cacheKey = $this->getCacheKey($userId, $roleId, $module);
                    Cache::tags([CacheEnum::TAG_ROLE])->forget($cacheKey);
                }
            }

            Log::info('DataPermissionCache cleared for role', [
                'role_id' => $roleId,
                'user_count' => count($userIds),
            ]);
        } catch (\Exception $e) {
            Log::error('DataPermissionCache clear role error', [
                'role_id' => $roleId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 清除模块所有缓存
     *
     * @param string $module 模块标识
     * @return void
     */
    public function clearModuleCache(string $module): void
    {
        if (!ModuleEnum::isValid($module)) {
            return;
        }

        try {
            $cacheKey = $this->getGlobalCacheKey($module);
            Cache::tags([CacheEnum::TAG_ROLE])->forget($cacheKey);

            Log::info('DataPermissionCache cleared for module', [
                'module' => $module,
            ]);
        } catch (\Exception $e) {
            Log::error('DataPermissionCache clear module error', [
                'module' => $module,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 清除所有缓存
     *
     * @return void
     */
    public function clearAllCache(): void
    {
        try {
            Cache::tags([CacheEnum::TAG_ROLE])->flush();

            Log::info('DataPermissionCache cleared all');
        } catch (\Exception $e) {
            Log::error('DataPermissionCache clear all error', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 批量清除缓存（组织变更、部门调动时调用）
     *
     * @param array $userIds 受影响的用户ID列表
     * @param string|null $module 指定模块，为空则清除所有模块
     * @return void
     */
    public function batchClearCache(array $userIds, ?string $module = null): void
    {
        try {
            $modules = $module ? [$module => ModuleEnum::all()[$module]] : ModuleEnum::all();

            foreach ($userIds as $userId) {
                foreach ($modules as $mod => $name) {
                    $roleIds = $this->roleUserDao->column(['user_id' => $userId, 'status' => 1], 'role_id');

                    foreach ($roleIds as $roleId) {
                        $cacheKey = $this->getCacheKey($userId, $roleId, $mod);
                        Cache::tags([CacheEnum::TAG_ROLE])->forget($cacheKey);
                    }
                }
            }

            Log::info('DataPermissionCache batch cleared', [
                'user_count' => count($userIds),
                'module' => $module ?? 'all',
            ]);
        } catch (\Exception $e) {
            Log::error('DataPermissionCache batch clear error', [
                'user_ids' => $userIds,
                'module' => $module,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 预热缓存（后台任务调用）
     *
     * @param int $userId 用户ID
     * @param int $roleId 角色ID
     * @param string $module 模块标识
     * @param ModulePermissionService $permissionService
     * @return array
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function warmupCache(
        int $userId,
        int $roleId,
        string $module,
        ModulePermissionService $permissionService
    ): array {
        $uids = $permissionService->getAccessibleUserIds($userId, $module);

        $this->setAccessibleUserIds($userId, $roleId, $module, $uids);

        return $uids;
    }

    /**
     * 获取缓存统计信息
     *
     * @return array
     */
    public function getCacheStats(): array
    {
        try {
            return [
                'tag' => CacheEnum::TAG_ROLE,
                'ttl' => self::CACHE_TTL,
                'prefix' => self::CACHE_PREFIX,
            ];
        } catch (\Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
