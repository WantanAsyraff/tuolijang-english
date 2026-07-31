<?php

declare(strict_types=1);


namespace App\Observers;

use App\Constants\CacheEnum;
use App\Http\Model\Config\Config;
use App\Http\Service\Config\SystemConfigService;
use App\Jobs\Config\SignStaffJob;
use Illuminate\Support\Facades\Cache;

class SystemConfigObserver
{
    private array $yiHaoTong = ['yihaotong_appid', 'yihaotong_appsecret'];

    public function updated(Config $systemConfig)
    {
        Cache::tags([CacheEnum::TAG_CONFIG])->flush();

        // 一号通配置变更时重新检查签名
        if (in_array($systemConfig->key, $this->yiHaoTong) && ! SystemConfigService::isBatchUpdatingConfig()) {
            app(SystemConfigService::class)->syncSignatureStatus();
        }

        // 电子签配置变更时触发员工签约任务
        if (
            $systemConfig->key === 'e_signature'
            && ! SystemConfigService::isSyncingSignatureStatus()
            && (int) $systemConfig->value === 1
            && (int) $systemConfig->getRawOriginal('value') !== 1
        ) {
            SignStaffJob::dispatch()->afterCommit();
        }
    }

    public function created(Config $systemConfig)
    {
        Cache::tags([CacheEnum::TAG_CONFIG])->flush();
    }
}
