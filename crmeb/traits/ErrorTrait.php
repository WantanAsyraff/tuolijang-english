<?php

declare(strict_types=1);


namespace crmeb\traits;

/**
 * Class BaseError.
 */
trait ErrorTrait
{
    /**
     * 错误信息.
     * @var string
     */
    protected $error;

    /**
     * 获取错误信息.
     * @return string
     */
    public function getError()
    {
        $error       = $this->error;
        $this->error = null;
        return $error;
    }

    /**
     * 设置错误信息.
     * @return bool
     */
    protected function setError(?string $error = null)
    {
        $this->error = $error ?: '未知错误';
        return false;
    }
}
