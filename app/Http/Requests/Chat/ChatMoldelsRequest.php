<?php

declare(strict_types=1);


namespace App\Http\Requests\Chat;

use App\Http\Requests\ApiValidate;
use Illuminate\Validation\Rule;

class ChatMoldelsRequest extends ApiValidate
{
    /**
     * 验证场景.
     * @var \string[][]
     */
    protected $scene = [
        'store'  => ['name', 'models_type','is_model','url','key'],
        'update' => ['name', 'models_type','is_model','url','key'],
    ];

    /**
     * 错误提醒.
     * @return string[]
     */
    public function message()
    {
        return [
            'name.required'         => '模型名称',
            'name.unique'           => '名称不能重复',
            'models_type.required'  => '模型类型',
            'is_model.required'     => '基础模型',
            'url.url'               => 'API URL'
        ];
    }

    /**
     * 规则.
     * @return array|string[]
     */
    protected function rules()
    {
        return [
            'name' => ['required', Rule::unique('chat_models')->ignore($this->route('id'))->whereNull('deleted_at')],
            'models_type'   => 'required',
            'is_model'      => 'required',
            'url'           => 'url'
        ];
    }
}
