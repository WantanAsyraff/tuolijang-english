<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Schedule\Schedule;
use Illuminate\Database\Eloquent\Model;

/**
 * 日程待办观察者
 */
class ScheduleObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_SCHEDULE;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof Schedule) {
            return [];
        }
        return $model->schedule_user()->pluck('uid')->toArray();
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof Schedule) {
            return [];
        }
        return $model->schedule_user()->pluck('uid')->toArray();
    }
}
