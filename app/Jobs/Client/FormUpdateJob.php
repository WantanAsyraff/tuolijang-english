<?php

declare(strict_types=1);


namespace App\Jobs\Client;

use App\Http\Service\Config\FormService;
use App\Http\Service\Open\OpenapiRuleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * 自定义表单修改后事件.
 */
class FormUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    /**
     * @var mixed|OpenapiRuleService
     */
    private mixed $service;

    /**
     * @var FormService|mixed
     */
    private mixed $formService;

    public function __construct(protected int $types, protected array $cateIds)
    {
        $this->service     = app()->get(OpenapiRuleService::class);
        $this->formService = app()->get(FormService::class);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $this->service->reloadCustomRuleParam($this->types);
            $this->formService->reloadCustomTableField($this->types, $this->cateIds);
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
