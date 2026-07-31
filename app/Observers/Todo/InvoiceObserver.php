<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Customer\Invoice;
use Illuminate\Database\Eloquent\Model;

/**
 * 发票待办观察者
 */
class InvoiceObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_INVOICE;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof Invoice) {
            return [];
        }
        return array_filter([$model->uid]);
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof Invoice) {
            return [];
        }
        return array_filter([$model->getOriginal('uid')]);
    }
}
