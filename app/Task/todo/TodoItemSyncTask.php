<?php

declare(strict_types=1);


namespace App\Task\todo;

use App\Constants\TodoEnum;
use App\Http\Service\Todo\TodoItemService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Log;

class TodoItemSyncTask extends Task
{
    /**
     * @param array<int, string> $types
     */
    public function __construct(protected array $types = []) {}

    public function handle(): void
    {
        $types = $this->types ?: TodoEnum::ALL_TYPES;
        $types = array_values(array_intersect(TodoEnum::ALL_TYPES, array_unique($types)));
        if (! $types) {
            return;
        }

        try {
            app()->get(TodoItemService::class)->syncByTypesForAllUsers($types);
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), [
                'types' => $types,
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTrace(),
            ]);
        }
    }
}
