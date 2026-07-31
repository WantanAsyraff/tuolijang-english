<?php

declare(strict_types=1);


namespace App\Task\crud;

use App\Http\Service\Crud\SystemCrudFieldService;
use Hhxsv5\LaravelS\Swoole\Task\Task;

/**
 *  触发器-字段更新.
 */
class TriggerFieldUpdateTask extends Task
{
    use TriggerTrait;

    /**
     * TriggerFieldUpdateTask constructor.
     */
    public function __construct(
        protected int $crudId,
        protected string $action,
        protected array $event,
        protected array $eventIds = [],
        protected int $dataId = 0,
        protected array $data = [],
        protected array $scheduleData = []
    ) {}

    public function runHandle()
    {
        $crudList = app()->get(SystemCrudFieldService::class)->formFieldUniqidByFieldCrud();
    }
}
