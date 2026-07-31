<?php

declare(strict_types=1);


namespace App\Http\Requests\user;

use App\Http\Requests\ApiValidate;

/**
 * 用户备忘录
 * Class UserMemorialRequest.
 */
class UserMemorialRequest extends ApiValidate
{
    /**
     * 自动验证
     * @var bool
     */
    public $authValidate = true;

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'content.required' => '请填写内容',
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'content' => 'required',
        ];
    }
}
