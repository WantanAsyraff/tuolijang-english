<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 替换客户标签.
 */
class ReplaceClientLabelJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected array $labelInfo, protected int $replaceLabelId) {}

    public function handle()
    {
        ReplaceCustomerClueLabelJob::dispatch($this->labelInfo, $this->replaceLabelId, 1, 50);
        ReplaceClientLabelUpdateJob::dispatch($this->labelInfo, $this->replaceLabelId, 1, 50);
    }
}
