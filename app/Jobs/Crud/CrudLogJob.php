<?php

declare(strict_types=1);


namespace App\Jobs\Crud;

use App\Http\Service\Crud\SystemCrudLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CrudLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected $data) {}

    public function handle()
    {
        app()->get(SystemCrudLogService::class)->create($this->data);
    }
}
