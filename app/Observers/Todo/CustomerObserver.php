<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Customer\Customer;
use Illuminate\Database\Eloquent\Model;

/**
 * 客户待办观察者
 */
class CustomerObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_CUSTOMER;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof Customer) {
            return [];
        }
        return array_filter([$model->uid]);
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof Customer) {
            return [];
        }
        return array_filter([$model->getOriginal('uid')]);
    }
}
