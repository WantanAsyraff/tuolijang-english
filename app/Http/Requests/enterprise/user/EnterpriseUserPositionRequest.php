<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\user;

use App\Http\Requests\ApiValidate;

/**
 * 任职经历
 * Class EnterpriseUserPositionRequest.
 */
class EnterpriseUserPositionRequest extends ApiValidate
{
    /**
     * 自动验证开启.
     * @var bool
     */
    public $authValidate = true;

    /**
     * 设置规则.
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'start_time' => 'required',
            'end_time'   => 'required|time_contrast_api:' . request('start_time'),
            'position'   => 'required',
            'department' => 'required',
        ];
    }

    /**
     * 设置错误提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'start_time.required'        => '请选择任职开始时间',
            'end_time.required'          => '请选择任职结束时间',
            'end_time.time_contrast_api' => '任职结束时间不能小于任职开始时间',
            'position.required'          => '请填写职位',
            'department.required'        => '请填写部门',
        ];
    }
}
