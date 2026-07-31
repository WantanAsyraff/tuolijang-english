<?php

declare(strict_types=1);


namespace App\Http\Controller\UniApi;

use App\Constants\CommonEnum;
use crmeb\basic\BaseController;

/**
 * 基础控制器
 * Class AuthController.
 * @property string $uuid 登录用户UUID
 * @property bool $isEnt 是否登录企业
 * @property array $userInfo 登录工作台用户信息
 * @property int $entId 当前工作台企业ID
 * @property array $entInfo 当前工作台公司信息
 */
class AuthController extends BaseController
{
    public string $origin = CommonEnum::ORIGIN_UNI;

    /**
     * @var string[]
     */
    protected $rule = [
        'uuid'     => 'uuId',
        'isEnt'    => 'isEnt',
        'entInfo'  => 'entInfo',
        'entId'    => 'entId',
        'userInfo' => 'userInfo',
    ];
}
