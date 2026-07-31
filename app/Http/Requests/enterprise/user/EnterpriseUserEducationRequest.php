<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\user;

use App\Http\Requests\ApiValidate;

/**
 * Class EnterpriseUserEducationRequest.
 */
class EnterpriseUserEducationRequest extends ApiValidate
{
    /**
     * 验证规则.
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'start_time'  => 'required',
            'end_time'    => 'required|time_contrast_api:' . request('start_time'),
            'school_name' => 'required',
            'major'       => 'required',
            'education'   => 'required',
        ];
    }

    /**
     * 错误提醒.
     * @return array
     */
    public function message()
    {
        return [
            'start_time.required'        => '请选择入学开始时间',
            'end_time.required'          => '请输入毕业时间',
            'end_time.time_contrast_api' => '毕业时间不能小于入学时间',
            'school_name.required'       => '请填写学校名称',
            'major.required'             => '请填写所学专业',
            'education.required'         => '请填写学历',
        ];
    }
}
