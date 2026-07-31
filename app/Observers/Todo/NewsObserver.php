<?php

declare(strict_types=1);


namespace App\Observers\Todo;

use App\Constants\TodoEnum;
use App\Http\Model\Admin\Admin;
use App\Http\Model\News\News;
use App\Http\Service\News\NewsVisitService;
use Illuminate\Database\Eloquent\Model;

/**
 * 通知待办观察者
 */
class NewsObserver extends BaseObserver
{
    protected function getType(): string
    {
        return TodoEnum::TYPE_NOTICE;
    }

    protected function getSourceId(Model $model): int
    {
        return (int) $model->id;
    }

    protected function getUserIdsBeforeDelete(Model $model): array
    {
        if (! $model instanceof News) {
            return [];
        }
        return $this->getUnreadUserIds((int) $model->id, (int) $model->entid);
    }

    protected function getUserIdsBeforeUpdate(Model $model): array
    {
        if (! $model instanceof News) {
            return [];
        }

        $originalStatus   = (int) $model->getOriginal('status');
        $originalPushTime = (string) $model->getOriginal('push_time');
        if ($originalStatus !== 1 || datetime_timestamp($originalPushTime) > time()) {
            return [];
        }

        return $this->getUnreadUserIds(
            (int) $model->id,
            (int) $model->getOriginal('entid')
        );
    }

    /**
     * @return array<int, int>
     */
    private function getUnreadUserIds(int $noticeId, int $entId): array
    {
        $visitService = app()->get(NewsVisitService::class);
        $readUserIds  = $visitService->dao->column(['notice_id' => $noticeId], 'user_id');

        return Admin::where('status', 1)
            ->when($entId > 0, fn ($query) => $query->where('entid', $entId))
            ->when(! empty($readUserIds), fn ($query) => $query->whereNotIn('id', $readUserIds))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }
}
