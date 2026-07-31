<?php

declare(strict_types=1);


namespace App\Jobs\Work;

use App\Http\Service\Work\WorkMemberService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WorkMemberJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected $next_cursor) {}

    public function handle()
    {
        app()->get(WorkMemberService::class)->authUpdataMemberV1($this->next_cursor);
    }
}
