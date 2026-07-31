<?php

declare(strict_types=1);


namespace App\Http\Requests\Chat;

use App\Http\Requests\ApiValidate;
use Illuminate\Validation\Rule;

class ChatAppMcpServiceRequest extends ApiValidate
{
    protected $scene = [
        'store'  => [ 'name'],
        'update' => [ 'name', 'status', 'sort'],
    ];

    public function message(): array
    {
        return [
            'name.required'          => '服务名称必填',
            'type.required'          => '连接类型必填',
            'type.in'                => '仅支持SSE(HTTP)模式，stdio不可配置',
            'service_url.required'   => 'MCP服务地址必填',
            'service_url.url'        => 'MCP服务地址格式不正确',
            'status.integer'         => '状态参数错误',
            'sort.integer'           => '排序参数错误',
            'info.max'               => '简介不能超过200个字符',
        ];
    }

    protected function rules(): array
    {
        return [
            'name'        => ['required', 'max:100'],
            'info'        => ['nullable', 'max:200'],
            'type'        => ['nullable', Rule::in(['sse'])],
            'service_url' => ['nullable', 'url'],
            'headers'     => ['nullable'],
            'status'      => ['integer', Rule::in([0, 1])],
            'sort'        => ['integer', 'min:0'],
        ];
    }
}
