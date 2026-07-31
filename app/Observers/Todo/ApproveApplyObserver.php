<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Events\BusinessDataChangeEvent;
use App\Http\Model\Approve\ApproveApply;
use Illuminate\Database\Eloquent\Model;

/**
 * 审批待办观察者
 */
class ApproveApplyObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_APPROVE_SUBMIT;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof ApproveApply) {
            return [];
        }
        $userIds = [$model->user_id];
        $verifierIds = $model->approve_users()->pluck('user_id')->toArray();
        return array_unique(array_filter(array_merge($userIds, $verifierIds)));
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof ApproveApply) {
            return [];
        }
        $userIds = [$model->getOriginal('user_id')];
        $verifierIds = $model->approve_users()->pluck('user_id')->toArray();
        return array_values(array_unique(array_filter(array_merge($userIds, $verifierIds))));
    }

    protected function dispatch(Model $model, string $action, array $userIds = []): void
    {
        try {
            foreach ([TodoEnum::TYPE_APPROVE_SUBMIT, TodoEnum::TYPE_APPROVE_PENDING] as $type) {
                BusinessDataChangeEvent::dispatch($type, $this->getSourceId($model), $action, $userIds);
            }
        } catch (\Throwable) {
            // 静默失败，避免影响业务
        }
    }
}
