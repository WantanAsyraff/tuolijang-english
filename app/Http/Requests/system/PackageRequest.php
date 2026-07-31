<?php

declare(strict_types=1);


namespace App\Http\Requests\system;

use App\Http\Requests\ApiValidate;

class PackageRequest extends ApiValidate
{
    /**
     * 开启自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 规则.
     * @var string[]
     */
    protected $rules = [
    ];

    /**
     * 提示.
     * @var string[]
     */
    protected $message = [
    ];
}
