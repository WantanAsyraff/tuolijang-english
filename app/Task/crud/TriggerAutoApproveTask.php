<?php

declare(strict_types=1);


namespace App\Task\crud;

use Hhxsv5\LaravelS\Swoole\Task\Task;

/**
 *  触发器-自动审批.
 */
class TriggerAutoApproveTask extends Task
{
    /**
     * TriggerAutoApproveTask constructor.
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

    public function handle()
    {
        if (! in_array($this->action, $this->event['action'])) {
            return true;
        }
    }
}
