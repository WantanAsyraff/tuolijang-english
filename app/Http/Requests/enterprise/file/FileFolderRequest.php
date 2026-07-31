<?php

declare(strict_types=1);


namespace App\Http\Requests\enterprise\file;

use App\Http\Requests\ApiValidate;

/**
 * 文件夹数据验证
 * Class FileFolderRequest.
 */
class FileFolderRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 验证规则.
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'path' => 'required',
        ];
    }

    /**
     * 错误提示.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写文件夹名称',
            'path.required' => '请选择上级分类',
        ];
    }
}
