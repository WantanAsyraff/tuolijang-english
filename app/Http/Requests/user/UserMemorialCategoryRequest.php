<?php

declare(strict_types=1);


namespace App\Http\Requests\user;

use App\Http\Requests\ApiValidate;

/**
 * 用户备忘录分类
 * Class UserMemorialCategoryRequest.
 */
class UserMemorialCategoryRequest extends ApiValidate
{
    /**
     * 自动.
     * @var bool
     */
    public $authValidate = true;

    /**
     * @return array|string[]
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required' => '请填写分类名称',
        ];
    }
}
