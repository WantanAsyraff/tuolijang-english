<?php

declare(strict_types=1);


namespace App\Listeners\Todo;

use App\Events\BusinessDataChangeEvent;
use App\Http\Service\Todo\TodoItemService;
use Illuminate\Support\Facades\Log;

/**
 * 业务数据创建监听器，同步创建对应待办.
 */
class TodoCreateListener
{
    public function handle(BusinessDataChangeEvent $event): void
    {
        if ($event->action !== 'create') {
            return;
        }

        try {
            app()->get(TodoItemService::class)->createBySourceId(
                $event->type,
                $event->sourceId,
                $event->userIds
            );
        } catch (\Throwable $e) {
            Log::error('TodoCreateListener sync failed', [
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
