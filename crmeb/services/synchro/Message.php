<?php

declare(strict_types=1);


namespace crmeb\services\synchro;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class Message.
 */
class Message extends TokenService
{
    public function setConfig()
    {
        return $this;
    }

    /**
     * @return array|mixed
     * @throws InvalidArgumentException
     */
    public function getMessageList()
    {
        return $this->httpRequest('/api/v2/message/lists', [], 'GET');
    }

    /**
     * 获取分类.
     * @return array|mixed
     * @throws InvalidArgumentException
     */
    public function getCateList()
    {
        return $this->httpRequest('/api/v2/message/cate', [], 'GET', false);
    }
}
