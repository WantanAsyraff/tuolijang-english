<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\file;

use App\Http\Requests\ApiValidate;
use Illuminate\Validation\Rule;

/**
 * 文件
 * Class FileRequest.
 */
class FileRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 规则.
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'type' => [
                'required', Rule::in(['word', 'ppt', 'excel']),
            ],
        ];
    }

    /**
     * 提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写文件名称',
            'type.required' => '请选择文档类型',
            'type.in'       => '文档类型不正确',
        ];
    }
}
