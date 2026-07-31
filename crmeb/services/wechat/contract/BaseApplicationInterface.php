<?php

declare(strict_types=1);


namespace crmeb\services\wechat\contract;

use EasyWeChat\MiniApp\Contracts\Application as MiniAppApplication;
use EasyWeChat\OfficialAccount\Contracts\Application as ApplicationInterface;
use EasyWeChat\Pay\Contracts\Application;
use EasyWeChat\Work\Contracts\Application as WorkApplication;

/**
 * Interface BaseApplicationInterface.
 */
interface BaseApplicationInterface
{
    /**
     * @email 136327134@qq.com
     * @date 2023/9/14
     */
    public function instance(): self;

    /**
     * @email 136327134@qq.com
     * @date 2023/9/14
     */
    public function application(): Application|ApplicationInterface|MiniAppApplication|WorkApplication;
}
