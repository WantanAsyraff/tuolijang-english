<?php

declare(strict_types=1);


namespace crmeb\services\upload;

use crmeb\basic\BaseManager;
use Illuminate\Support\Facades\Config;

/**
 * Class Upload.
 * @mixin \crmeb\services\upload\storage\Local
 * @mixin \crmeb\services\upload\storage\OSS
 * @mixin \crmeb\services\upload\storage\Qiniu
 * @mixin \crmeb\services\upload\storage\Jdoss
 * @mixin \crmeb\services\upload\storage\Tyoss
 * @mixin \crmeb\services\upload\storage\Cos
 * @mixin \crmeb\services\upload\storage\Obs
 */
class Upload extends BaseManager
{
    /**
     * 空间名.
     * @var string
     */
    protected $namespace = '\crmeb\services\upload\storage\\';

    /**
     * 设置默认上传类型.
     * @return mixed
     */
    protected function getDefaultDriver()
    {
        return Config::get('upload.default', 'local');
    }
}
