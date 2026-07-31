---
name: generate-api
description: 在 api/<module>.ts 中按项目统一风格批量生成请求函数（xxxApi 命名 + JSDoc + request.get/post/put/delete 调用）。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "加一个接口"
    - "把这些接口生成到 api/xx"
    - "对接 xx 接口"
    - "把 swagger 转成 api 函数"
---

# generate-api

## 何时触发

用户说"加一个接口"、"把这些接口生成到 `api/xx`"、"对接 xx 接口"、"把这段 swagger/Apifox 描述转成 api 函数"。

> 如果是新模块且页面也要一起生成，用 [`generate-crud-module`](../generate-crud-module/SKILL.md) 一次性搞定。

## 项目 API 层约定

读 `api/customer.ts` 和 `utils/request.ts` 确认风格，关键点：

1. **入口**：`import request from "../utils/request";`（**不是** `@/utils/request`，这是模块文件夹的相对路径约定）
2. **可用方法**：`request.get / post / put / delete / head / options / trace / connect`（见 `utils/request.ts:102`）
3. **函数命名**：一律 `<动作><实体>Api` 驼峰命名 + `Api` 后缀
   - ✅ `customerListApi` `clientInvoiceMarkApi` `salesmanCustomApi`
   - ❌ `getCustomer` `fetchCustomerList` `customerList`
4. **JSDoc 注释**：单行简述 + `@return {*}`，参数有需要时加 `@param`
5. **path 参数**：用模板字符串 `` `client/customer/return/${id}` ``
6. **query / body**：第二参数 `data: object`，统一签名
7. **可选第三参数**：`{ noAuth?: boolean, noVerify?: boolean }`（见 `utils/request.ts:36` 的 baseRequest 签名）

## 输入要求

| 项 | 说明 |
|---|---|
| 目标文件 | 新建 `api/<module>.ts` 还是追加到现有？ |
| 接口清单 | 每条至少包含：method / path / 用途，最好含入参/返回 |
| 命名前缀 | 如统一用 `order` / `clientOrder`，影响函数名 |

接口清单接受多种格式：
- 手写："GET `/order` 获取订单列表"
- Apifox / swagger 段落（粘贴即可）
- 表格

不接受"模糊描述"（如"加几个订单相关接口"），必须列出明确路径。

## 执行步骤

### 1. 决定目标文件

- 若 `api/<module>.ts` 已存在 → 用 Edit 在文件末尾追加（注意保留末尾换行）
- 若不存在 → 用 Write 新建，第一行 `import request from "../utils/request";` 紧跟空行

### 2. 为每条接口生成函数

模板：

```ts
/**
 * <用途中文描述>
 * @return {*}
 */
export function <name>Api(<args>) {
  return request.<method>(<url>, <data?>);
}
```

参数推断规则：

| path 形式 | 函数签名 | url 形式 |
|---|---|---|
| 无占位符，无 body | `()` | `"client/order"` |
| 无占位符，有 body/query | `(data: object)` | `"client/order"`, `data` |
| 单个 path 参数 | `(id: number)` | `` `client/order/${id}` `` |
| path 参数 + body | `(id: number, data: object)` | `` `client/order/${id}` ``, `data` |
| 多个 path 参数 | `(id: number, type: string, data?: object)` | `` `client/order/${id}/${type}` `` |

method 映射：
- `GET` → `request.get`
- `POST` → `request.post`
- `PUT` → `request.put`
- `DELETE` → `request.delete`

### 3. 命名规范

按"动作 + 实体 + Api"组合：

| 接口语义 | 推荐函数名 |
|---|---|
| 列表 | `<entity>ListApi` |
| 详情 | `<entity>DetailApi` |
| 新增 | `<entity>CreateApi` 或 `<entity>AddApi` |
| 更新 | `<entity>UpdateApi` |
| 删除 | `<entity>DeleteApi` |
| 状态变更（如"撤回"） | `<entity>WithdrawApi` `<entity>ReturnApi`（参照 `api/customer.ts:15`） |
| 业务相关动作 | `<entity><Action>Api`，如 `clientInvoiceMarkApi` |

冲突时优先采用 `api/customer.ts` 已有命名作参照。

### 4. 输出报告

```
已在 api/<module>.ts 生成 N 个函数：

- xxxListApi(data)             GET    client/xxx
- xxxDetailApi(id)             GET    client/xxx/:id
- xxxCreateApi(data)           POST   client/xxx
- ...

⚠️ 需人工确认:
1. 函数名是否与 api/ 下其他文件冲突（可 grep "xxxApi" 检查）
2. 入参类型 object 是否需要细化成具体 interface（如 { id: number; name: string }）
3. 返回值是否需要泛型（项目目前多数函数返回 any，按需收紧）
```

## 红线

- ❌ 禁止使用 `axios` / `fetch`，统一走 `utils/request`
- ❌ 禁止改 `utils/request.ts`（如真有问题先告诉用户）
- ❌ 禁止函数名不带 `Api` 后缀
- ❌ 禁止把多个接口塞进一个函数（一个 export = 一个端点）
- ❌ 禁止 JSDoc 中文混杂英文术语缩写（如 "get list of customer 的接口"），中文文档就纯中文
