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
 * 保存客户信息
 * Class WorkSaveClientInfoJob.
 */
class WorkSaveClientInfoJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected string $corpId, protected string $externalUserid, protected string $userid = '', protected bool $isUpdate = true) {}

    public function handle()
    {
        app()->get(WorkClientService::class)->saveOrUpdateClient($this->corpId, $this->externalUserid, $this->userid, $this->isUpdate);
    }
}
