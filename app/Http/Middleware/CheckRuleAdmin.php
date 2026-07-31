<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Services\system\auth\SystemAdminServices;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\services\CoreBusinessService;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;

/**
 * Class CheckRuleAdmin.
 */
class CheckRuleAdmin implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * 核心业务服务
     */
    protected CoreBusinessService $coreService;

    public function __construct(CoreBusinessService $coreService)
    {
        $this->coreService = $coreService;
    }

    /**
     * 前置.
     * @return mixed|void
     */
    public function before(Request $request)
    {
        $uri = $request->route()->uri();
        $adminInfo = $request->adminInfo();

        if ($adminInfo && isset($adminInfo['level']) && $adminInfo['level']) {
            // 使用核心服务进行权限验证
            if (!$this->coreService->validateAdminPermission($uri, $adminInfo, $request->method())) {
                abort(403, '权限验证失败');
            }

            /** @var SystemAdminServices $service */
            $service = app()->get(SystemAdminServices::class);
            $service->checkAuth($uri, $adminInfo, $request->method());
        }
    }

    /**
     * 后置.
     * @return mixed|void
     */
    public function after($response)
    {
        // TODO: Implement after() method.
    }
}
