<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi;

use App\Constants\CommonEnum;
use App\Constants\UserEnum;
use App\Http\Service\Admin\AdminService;
use crmeb\services\wechat\Work;
use crmeb\services\wechat\WechatException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;

/**
 * 企业微信控制器.
 */
#[Prefix('uni/work')]
class WorkController extends AuthController
{
    /**
     * 获取企业微信配置.
     * @return Application|\Illuminate\Http\Response|Response|ResponseFactory
     */
    #[Get('config', '获取企业微信配置')]
    public function config(Request $request, Work $work)
    {
        return response(['status' => 200, 'msg' => 'ok', 'data' => $work->getJsSDK($request->get('url', ''))], 200, [], 'json');
    }

    /**
     * 获取应用配置.
     * @return Application|\Illuminate\Http\Response|ResponseFactory
     * @throws InvalidArgumentException
     */
    #[Get('agent_config', '获取应用配置')]
    public function agentConfig(Request $request, Work $work)
    {
        Log::info('wxwork agent_config requested', [
            'url'       => $request->get('url', ''),
            'referer'   => $request->headers->get('referer', ''),
            'userAgent' => mb_substr((string) $request->headers->get('user-agent', ''), 0, 200),
        ]);

        try {
            return response(['status' => 200, 'msg' => 'ok', 'data' => $work->getAgentConfig($request->get('url', ''))], 200, [], 'json');
        } catch (WechatException $e) {
            return response(['status' => 400, 'msg' => $e->getMessage(), 'message' => $e->getMessage(), 'data' => []], 200, [], 'json');
        }
    }

    /**
     * 企业微信扫码登录.
     * @return mixed
     * @throws BindingResolutionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    #[Post('work_auth_login', '企业微信扫码登录')]
    public function workAuthLogin(Request $request, AdminService $services)
    {
        $code = $request->post('code', '');
        if (! $code) {
            return $this->fail('缺少CODE');
        }

        return $this->success($services->workAuthLogin(
            $code,
            CommonEnum::ORIGIN_UNI,
            (string) $request->header('Form-type'),
            isSingle: UserEnum::SINGLE_LOGIN
        ));
    }
}
