---
name: oa-request-validation
description: 在陀螺匠 OA 项目中新增或修改请求验证、参数接收、错误提示、统一响应格式时使用。适用于 ApiValidate、ApiRequest、Request rules/messages 和 Controller 入参处理。
---

# OA 请求验证

## 适用场景

新增接口参数、修复参数校验、处理表单提交、调整错误提示、生成接口文档字段说明时使用。

## Request 类选择

- 大多数业务验证继承 `ApiValidate`。
- 已有模块如果使用 `ApiRequest`，新增同模块 Request 时保持一致。
- 不确定时先搜索同目录相似 Request。

## 基本结构

```php
namespace App\Http\Requests\Example;

use App\Http\Requests\ApiValidate;

class ExampleRequest extends ApiValidate
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:64',
            'status' => 'required|integer|in:0,1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '名称不能为空',
            'status.in' => '状态值错误',
        ];
    }
}
```

## Controller 使用

- 读 GET 参数：优先沿用现有 `$this->request->getMore([...])` 或 Request 封装。
- 读 POST/PUT 参数：优先沿用 `$request->postMore([...])`、`$request->all()` 或同模块模式。
- 不在 Controller 手写大段参数过滤。
- 成功响应使用 `$this->success(...)`，失败使用 `$this->fail(...)`。

## 响应注意

项目里 `success()` 和 `fail()` 参数顺序容易混淆。调用前查看当前继承类定义或相近 Controller。

常见写法：

```php
return $this->success($data);
return $this->success('操作成功');
return $this->fail('错误信息');
```

## 校验规则建议

- ID：`required|integer|min:1`
- 枚举：使用 `in:...`，复杂枚举优先常量化。
- 金额：结合项目字段类型选择 `numeric|min:0` 或整数分。
- 数组：同时校验 `field` 和 `field.*`。
- 日期：明确格式，如 `date_format:Y-m-d`。
- 文件：查看上传模块现有规则，不自行放宽类型和大小。

## 安全检查

- 禁止接收客户端传入 `uid`、`entid`、`tenant_id` 等可由登录态推导的敏感归属字段，除非已有权限校验。
- 禁止在响应中返回 password、token、secret、access_key 等敏感字段。
- 批量 ID 操作必须校验数组长度和每项类型。
