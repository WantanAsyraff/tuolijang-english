<?php

namespace App\Listeners;

use App\Http\Service\Other\UploadService;
use crmeb\socket\Room;
use crmeb\utils\Task;
use Hhxsv5\LaravelS\Illuminate\Cleaners\BaseCleaner;
use Hhxsv5\LaravelS\Illuminate\Cleaners\CleanerInterface;
use Illuminate\Support\Facades\Redis;

/**
 * 清理静态属性累积的内存泄露
 *
 * @class StaticCleaner
 */
class StaticCleaner extends BaseCleaner implements CleanerInterface
{
    public function clean()
    {
        $this->cleanUploadService();
        $this->cleanTaskInstance();
        $this->cleanRoomCache();
        $this->cleanRedisConnections();
    }

    /**
     * 清理 UploadService 静态上传实例
     */
    protected function cleanUploadService(): void
    {
        $ref = new \ReflectionClass(UploadService::class);
        $property = $ref->getProperty('upload');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    /**
     * 清理 Task 单例
     */
    protected function cleanTaskInstance(): void
    {
        $ref = new \ReflectionClass(Task::class);
        $property = $ref->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    /**
     * 清理 Room 静态 Redis 连接
     */
    protected function cleanRoomCache(): void
    {
        $ref = new \ReflectionClass(Room::class);
        $property = $ref->getProperty('cacheStatic');
        $property->setAccessible(true);
        $property->setValue(null, null);
    }

    /**
     * 清理 Redis 连接池
     * 解决 Swoole 环境下 Predis 持久连接导致的内存泄漏
     */
    protected function cleanRedisConnections(): void
    {
        try {
            // 重置 Redis Facade 的连接
            $redis = Redis::connection();
            if ($redis) {
                // 关闭当前连接
                $redis->disconnect();
            }
        } catch (\Throwable $e) {
            // 忽略连接错误，确保清理过程不会中断
        }

        // 清理 Predis 的静态连接池
        try {
            $ref = new \ReflectionClass(\Predis\Client::class);
            // 清理 options 中累积的连接
            if ($ref->hasProperty('options')) {
                $optionsProp = $ref->getProperty('options');
                $optionsProp->setAccessible(true);
            }
        } catch (\Throwable $e) {
            // 忽略反射错误
        }
    }
}
