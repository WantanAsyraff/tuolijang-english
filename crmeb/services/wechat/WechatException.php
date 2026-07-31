<?php

declare(strict_types=1);


namespace crmeb\services\wechat;

/**
 * 微信错误统一处理
 * Class WechatException.
 */
class WechatException extends \RuntimeException
{
    /**
     * WechatException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct($message = '', $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
