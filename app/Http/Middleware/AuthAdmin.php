<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Service\Crud\SystemCrudQuestionnaireService;
use crmeb\exceptions\AdminException;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
 * 后台授权登录
 * Class AuthAdmin.
 */
class AuthAdmin implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * 前置事件.
     */
    public function before(Request $request)
    {
        try {
            $admin = auth('admin')->userOrFail();
        } catch (TokenExpiredException) {
            $admin = $this->resolveQuestionnaireUser($request);
        } catch (\Exception) {
            $admin = $this->resolveQuestionnaireUser($request);
        }

        if (isset($admin)) {
            $request->macro('admin', $admin);
            $request->macro('userInfo', function (?string $key = null) use ($admin) {
                $admin = $admin->toArray();
                if ($key) {
                    return $admin[$key] ?? null;
                }
                return $admin;
            });
            $request->macro('uuId', function () use ($admin) {
                return $admin->uid;
            });
        }
    }

    /**
     * 后置事件.
     * @param mixed $response
     * @return mixed|void
     */
    public function after($response)
    {
        $this->request = null;
    }

    /**
     * 兼容调查问卷匿名访问；普通登录失效统一交给前端 refresh_token 续期.
     */
    private function resolveQuestionnaireUser(Request $request)
    {
        $userInfo = app()->get(SystemCrudQuestionnaireService::class)->checkUniqueisLogin($request);
        if (is_bool($userInfo)) {
            throw new AdminException('Login expired, please login again', 410003);
        }
        return $userInfo;
    }
}
