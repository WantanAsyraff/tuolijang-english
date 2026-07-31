<?php

declare(strict_types=1);


namespace App\Constants;

use MyCLabs\Enum\Enum;

/**
 * 云存储方式.
 */
final class StorageEnum extends Enum
{
    /**
     * 上传方式：本地.
     */
    public const UPLOAD_LOCAL = 1;

    /**
     * 上传方式：七牛云.
     */
    public const UPLOAD_QINIU = 2;

    /**
     * 上传方式：阿里云.
     */
    public const UPLOAD_ALI = 3;

    /**
     * 上传方式：腾讯云.
     */
    public const UPLOAD_TX = 4;

    /**
     * 上传方式：京东云.
     */
    public const UPLOAD_JD = 5;

    /**
     * 上传方式：华为云.
     */
    public const UPLOAD_HW = 6;

    /**
     * 上传方式：天翼云.
     */
    public const UPLOAD_TY = 7;
}
