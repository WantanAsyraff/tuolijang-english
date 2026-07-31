<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Customer\Contract;
use Illuminate\Database\Eloquent\Model;

/**
 * 合同待办观察者
 */
class ContractObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_CONTRACT;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof Contract) {
            return [];
        }
        return array_filter([$model->uid]);
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof Contract) {
            return [];
        }
        return array_filter([$model->getOriginal('uid')]);
    }
}
