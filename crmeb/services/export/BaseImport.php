<?php

declare(strict_types=1);


namespace crmeb\services\export;

use App\Task\export\ImportTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;

abstract class BaseImport
{
    public string $filePath = '';

    public string $tableName = '';

    public array $fieldMap = [];

    public function __construct(protected int $recordId = 0)
    {
        $this->tableName = $this->tableName ?: $this->setTable();
        $this->fieldMap  = $this->fieldMap ?: $this->setFieldMap();
        Task::deliver(new ImportTask($this, $recordId));
    }

    abstract public function processCallback(): callable;

    abstract public function setTable(): string;

    abstract public function setFieldMap(): array;
}
