<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Http\Service\Customer\FollowUpService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClientFollowAIAuthSaveJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected int $clientId, protected string $external_userid, protected string $userid) {}

    public function handle()
    {
        app()->make(FollowUpService::class)->authSave($this->clientId, $this->external_userid, $this->userid);
    }
}
