<?php

declare(strict_types=1);


namespace App\Jobs\Assess;

use App\Http\Service\Admin\AdminService;
use App\Http\Service\Assess\AssessPlanService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

class AssessAbnormalJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private const PERIODS = [1, 2, 3, 4, 5];

    public $tries = 1;

    public $timeout = 80;

    public $failOnTimeout = true;

    public $uniqueFor = 600;

    public function __construct(private ?int $period = null) {}

    public static function dispatchPeriods(): void
    {
        foreach (self::PERIODS as $period) {
            self::dispatch($period);
        }
    }

    public function uniqueId(): string
    {
        return (string) ($this->period ?? 'all');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function handle()
    {
        if (is_null($this->period)) {
            self::dispatchPeriods();
            return;
        }

        $service = app()->get(AssessPlanService::class);
        try {
            $userIds = app()->get(AdminService::class)->column(['status' => 1], 'id');
            if ($userIds) {
                $service->abnormalTimer(1, $userIds, $this->period);
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine(), 'trace' => $e->getTrace()]);
        }
    }
}
