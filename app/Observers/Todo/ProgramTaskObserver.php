<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Program\ProgramTask;
use Illuminate\Database\Eloquent\Model;

/**
 * 项目任务待办观察者
 */
class ProgramTaskObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_TASK;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof ProgramTask) {
            return [];
        }
        return array_filter([$model->uid]);
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof ProgramTask) {
            return [];
        }
        return array_filter([$model->getOriginal('uid')]);
    }
}
