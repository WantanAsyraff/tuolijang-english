<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use App\Http\Service\Customer\LeadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * 绑定企微线索池数据处理.
 */
class WorkWithClueJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected int $uid, protected string $userId, protected int $page = 1, protected mixed $customerService = null, protected mixed $liaisonService = null, protected mixed $recordService = null, protected array $customerFieldMap = []) {}

    public function handle()
    {
        app()->get(LeadService::class)->clueConnectWork($this->uid, $this->userId, $this->page, $this->customerService, $this->liaisonService, $this->recordService, $this->customerFieldMap);
    }
}
