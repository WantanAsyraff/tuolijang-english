<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use App\Http\Service\Work\WorkClientService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 设置客户标签
 * Class WorkClientSetLabelJob.
 */
class WorkClientSetLabelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected array $item) {}

    public function handle()
    {
        app()->get(WorkClientService::class)->setClientMarkTag($this->item);
    }
}
