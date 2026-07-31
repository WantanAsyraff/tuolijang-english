<?php

declare(strict_types=1);


namespace App\Http\Middleware;

use App\Http\Service\Open\OpenApiKeyService;
use crmeb\exceptions\AdminException;
use crmeb\interfaces\ApiMiddlewareInterface;
use crmeb\traits\MiddlewareTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
 * 后台授权登录
 * Class AuthAdmin.
 */
class AuthOpenApi implements ApiMiddlewareInterface
{
    use MiddlewareTrait;

    /**
     * @var null|\Closure|mixed|object
     */
    private mixed $token = '';

    /**
     * 前置事件.
     */
    public function before(Request $request)
    {
        try {
            auth('openapi')->userOrFail();
        } catch (TokenExpiredException) {
            $this->token = auth('openapi')->refresh();
        } catch (\Exception) {
            throw new AdminException('Login expired, please login again', 410003);
        }
        $request->authSkInfo = app()->get(OpenApiKeyService::class)->check();
    }

    /**
     * 后置事件.
     * @return mixed|void
     */
    public function after($response)
    {
        $this->request = null;
        if ($this->token) {
            $response->headers->set('Authorization', 'Bearer ' . $this->token);
            return $response;
        }
    }
}
