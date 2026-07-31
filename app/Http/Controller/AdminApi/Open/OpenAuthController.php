<?php

declare(strict_types=1);


namespace App\Http\Controller\AdminApi\Open;

use App\Http\Controller\AdminApi\AuthController;
use App\Http\Service\Open\OpenApiKeyService;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 对外接口授权
 */
#[Prefix('open/auth')]
class OpenAuthController extends AuthController
{
    public function __construct(OpenApiKeyService $service)
    {
        parent::__construct();
        $this->service = $service;
    }
    #[Post('login', '接口授权登录')]
    protected function login(): mixed
    {
        [$accessKey,$secretKey] = $this->request->postMore([
            ['access_key',''],
            ['secret_key',''],
        ], true);
        return $this->success($this->service->login($accessKey, $secretKey));
    }

}
