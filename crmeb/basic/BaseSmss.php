<?php

declare(strict_types=1);


namespace crmeb\basic;

use crmeb\services\sms\TokenService;

/**
 * Class BaseSmss.
 */
abstract class BaseSmss extends BaseStorage
{
    /**
     * access_token.
     * @var null
     */
    protected $accessToken;

    /**
     * BaseSmss constructor.
     */
    public function __construct(string $name, TokenService $accessTokenServeService, string $configFile)
    {
        $this->accessToken = $accessTokenServeService;
        $this->name        = $name;
        $this->configFile  = $configFile;
        $this->initialize();
    }

    /**
     * 开通服务
     * @return mixed
     */
    abstract public function open();

    /**
     * 修改签名.
     * @return mixed
     */
    abstract public function modify(?string $sign = null);

    /**
     * 用户信息.
     * @return mixed
     */
    abstract public function info();

    /**
     * 发送短信
     * @return mixed
     */
    abstract public function send(string $phone, string $templateId, array $data);

    /**
     * 短信模板
     * @return mixed
     */
    abstract public function temps(int $page, int $limit, int $type);

    /**
     * 申请模板
     * @return mixed
     */
    abstract public function apply(string $title, string $content, int $type);

    /**
     * 模板记录.
     * @return mixed
     */
    abstract public function applys(int $tempType, int $page, int $limit);

    /*发送记录
     * @return mixed
     */
    abstract public function record($page, $limit);

    /**
     * 初始化.
     * @return mixed|void
     */
    protected function initialize(array $config = []) {}
}
