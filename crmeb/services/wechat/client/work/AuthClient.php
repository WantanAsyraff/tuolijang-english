<?php

declare(strict_types=1);


namespace crmeb\services\wechat\client\work;

use crmeb\services\wechat\client\BaseClient;
use crmeb\services\wechat\WechatException;
use EasyWeChat\Kernel\HttpClient\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * 获取用户信息.
 */
class AuthClient extends BaseClient
{
    /**
     * 获取用户信息.
     * @return Response|ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function getUserinfo(string $code)
    {
        $userInfo = $this->api->get('cgi-bin/auth/getuserinfo', ['code' => $code])->toArray();
        if ($userInfo['errcode'] !== 0) {
            throw new WechatException($userInfo['errmsg']);
        }
        return $userInfo;
    }
}
