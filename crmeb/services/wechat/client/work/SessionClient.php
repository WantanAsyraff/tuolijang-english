<?php

declare(strict_types=1);


namespace crmeb\services\wechat\client\work;

use crmeb\services\HttpService;
use crmeb\services\wechat\config\WorkConfig;
use crmeb\services\wechat\WechatException;

/**
 * 会话内容存档.
 */
class SessionClient
{
    /**
     * 接口地址
     * @var string
     */
    protected $baseUrl = 'http://127.0.0.1:8000';

    /**
     * 配置.
     */
    protected array $config = [];

    /**
     * 基础数据.
     */
    protected array $baseData = [];

    public function __construct(WorkConfig $config)
    {
        $envUrl = env('WORK_SESSION_URL');
        if ($envUrl) {
            $this->baseUrl = $envUrl;
        }
        $this->config   = $config->getAppConfig(WorkConfig::TYPE_SESSION);
        $this->baseData = [
            'corp_id'         => $config->getCorpId(),
            'corp_secret'     => $this->config['secret'] ?? '',
            'private_version' => $this->config['public_key_version'] ?? '',
            'private_key'     => $this->config['private_key'] ?? '',
        ];
    }

    /**
     * 基础请求
     * @return array|string
     */
    public function baseRequest(string $api, array $data)
    {
        [$code, $response] = (new HttpService())->setHeader(['content-type: application/json'])->requests($api, 'POST', json_encode(array_merge($this->baseData, $data), JSON_UNESCAPED_UNICODE), true);
        $data = json_decode($response, true);
        if ($code === 200) {
            return $data;
        }
        throw new WechatException('获取会话存档失败:' . ($data['message'] ?? '未知错误'));
    }

    /**
     * 获取会话内容存档数据.
     * @return array
     */
    public function getDecryptChatData(array $data)
    {
        return $this->baseRequest($this->baseUrl . '/api/wxmessage/chat_data', $data);
    }

    /**
     * @return array|string
     */
    public function getMediaData(array $data)
    {
        return $this->baseRequest($this->baseUrl . '/api/wxmessage/media_data', $data);
    }
}
