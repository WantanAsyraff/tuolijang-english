<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use crmeb\exceptions\ApiException;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;

/**
 * 检测App版本号
 * Class CheckEnterprise.
 */
class CheckVersion implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    private array $whiteVersion = ['1.8', '1.7'];

    /**
     * 前置事件.
     * @return mixed
     */
    public function before(Request $request)
    {
        $platform = $request->header('Form-Type', 'h5');
        if (strtolower($platform) === 'app') {
            $appVersion = $request->header('AppVersion', '');
            if (! $appVersion) {
                throw new ApiException('版本信息不匹配', 410005);
            }
            $version       = getVersion('version_num');
            $appVersion    = explode('.', $appVersion);
            $systemVersion = explode('.', $version);
            if (! in_array($appVersion[0] . '.' . $appVersion[1], $this->whiteVersion) && ($appVersion[0] != $systemVersion[0] || $appVersion[1] != $systemVersion[1])) {
                throw new ApiException('版本信息不匹配，请更新App版本至: v' . $version . '版本', 410005);
            }
        }
    }

    /**
     * 后置中间件.
     * @param mixed $response
     * @return mixed
     */
    public function after($response)
    {
        return $response->withHeaders(['AppVersion' => getVersion('version_num')]);
    }
}
