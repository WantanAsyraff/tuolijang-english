<?php

declare(strict_types=1);


namespace App\Listeners;

use crmeb\services\wechat\config\HttpCommonConfig;
use crmeb\services\wechat\config\WorkConfig;
use crmeb\services\wechat\Work;
use Hhxsv5\LaravelS\Illuminate\Cleaners\BaseCleaner;
use Hhxsv5\LaravelS\Illuminate\Cleaners\CleanerInterface;
use Illuminate\Support\Facades\Facade;

/**
 * Class WorkCleaner.
 */
class WorkCleaner extends BaseCleaner implements CleanerInterface
{
    protected $instances = [
        HttpCommonConfig::class,
        Work::class,
        WorkConfig::class,
    ];

    public function clean()
    {
        foreach ($this->instances as $instance) {
            $this->currentApp->forgetInstance($instance);
            Facade::clearResolvedInstance($instance);
        }
    }
}
