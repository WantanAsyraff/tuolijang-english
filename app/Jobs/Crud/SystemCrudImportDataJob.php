<?php

declare(strict_types=1);


namespace App\Jobs\Crud;

use App\Http\Service\Crud\CrudModuleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SystemCrudImportDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public function __construct(protected $crud, protected array $datas, protected int $uid) {}

    public function handle()
    {
        app()->get(CrudModuleService::class)->importData($this->crud, $this->datas, $this->uid);
    }
}
