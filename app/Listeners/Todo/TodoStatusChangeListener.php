<?php

declare(strict_types=1);


namespace App\Listeners\Todo;

use App\Events\BusinessDataChangeEvent;
use App\Http\Service\Todo\TodoItemService;
use Illuminate\Support\Facades\Log;

/**
 * 业务数据变更监听器
 * 监听模型观察者触发的变更事件，同步更新待办状态.
 */
class TodoStatusChangeListener
{
    /**
     * Handle the event.
     */
    public function handle(BusinessDataChangeEvent $event): void
    {
        if ($event->action === 'create') {
            return;
        }

        try {
            $service = app()->get(TodoItemService::class);
            if ($event->action === 'delete') {
                $service->deleteBySourceId($event->type, $event->sourceId, $event->userIds);
            } else {
                $service->updateBySourceId($event->type, $event->sourceId, $event->userIds);
            }
        } catch (\Throwable $e) {
            Log::error('TodoStatusChangeListener sync failed', [
                'type'     => $event->type,
                'sourceId' => $event->sourceId,
                'action'   => $event->action,
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
            ]);
        }
    }
}
