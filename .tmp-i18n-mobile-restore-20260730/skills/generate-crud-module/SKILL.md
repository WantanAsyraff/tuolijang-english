---
name: generate-crud-module
description: 基于 customer / opportunity 等现有业务模块的同构结构，一键生成新业务模块的完整 CRUD 脚手架（api + 列表 + 详情 + 表单 + 搜索 + 路由注册）。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "新建一个 xx 业务模块"
    - "生成 xx 模块的 CRUD"
    - "照着 customer 模块做一个 xx"
---

# generate-crud-module

## 何时触发

用户说"新建一个 **xx** 业务模块"、"生成 **xx** 模块的列表/详情/新增"、"照着 customer / opportunity 做一个 **xx**"、"加一个新的业务实体 **xx**"。

> 如果用户只想加单个页面，用 [`generate-page`](../generate-page/SKILL.md)。
> 如果用户只想加 API 函数，用 [`generate-api`](../generate-api/SKILL.md)。

## 输入要求

调用前，**必须**从用户处确认以下信息（缺一项就追问，不要瞎猜）：

| 项 | 示例 | 说明 |
|---|---|---|
| 模块英文名 | `order` | 用作目录名、API 路径前缀、函数前缀 |
| 模块中文名 | `订单` | 用于 `navigationBarTitleText` 与 message 文案 |
| 后端 API 基路径 | `client/order` | 拼到 `request.get/post` 第一参数 |
| 主要字段列表 | `order_no` `customer_name` `amount` `status` `created_at` | 列表显示 + 表单录入需要 |
| 是否需要顶部 tab 切换 | 是 / 否（如"全部 / 进行中 / 已完成"） | 影响 index.vue 结构 |
| 是否需要搜索页 | 是 / 否 | 决定是否生成 `search.vue` |
| 平台限制 | 全平台 / 仅 H5 / 仅 APP | 决定 `pages.json` 是否包裹 `#ifdef` |

## 参照模板

**优先复制并改名 `pages/customer/` 下结构最接近的子模块**，而不是凭空造。推荐参照：

- 标准 CRUD：`pages/customer/list/`（包含 `index.vue` 列表 / `details.vue` 详情 / `addCustomer.vue` 新增 / `search.vue` 搜索 / `components/`）
- 含商机/订单类（带产品行）：`pages/customer/opportunity/`（包含 `add-product.vue` / `edit-price.vue` 这类子操作）
- API 函数风格：`api/customer.ts`（导出 `xxxApi` 函数，统一 JSDoc）
- 列表 + filter 组合：`pages/customer/list/index.vue` 顶层 + `components/formBox.vue`

执行前用 Read 把上述至少 1 份模板文件完整读一遍，确保生成代码与项目风格一致。

## 执行步骤

### 1. 生成 API 层

新建 `api/<module>.ts`，按以下命名导出函数（参照 `api/customer.ts:7-50` 风格）：

```ts
import request from "../utils/request";

/** 列表 */ export function <module>ListApi(data: object) { return request.get("<base>", data); }
/** 详情 */ export function <module>DetailApi(id: number) { return request.get(`<base>/${id}`); }
/** 新增 */ export function <module>CreateApi(data: object) { return request.post("<base>", data); }
/** 更新 */ export function <module>UpdateApi(id: number, data: object) { return request.put(`<base>/${id}`, data); }
/** 删除 */ export function <module>DeleteApi(id: number) { return request.delete(`<base>/${id}`); }
```

JSDoc 注释统一用 `@return {*}`（项目主流风格）。

### 2. 生成页面文件

在 `pages/<module>/` 下创建：

| 文件 | 模板来源 | 关键点 |
|---|---|---|
| `index.vue` | `pages/customer/list/index.vue` | 必须套 `BaseContainer` + `defaultNavBar` + `globalIndex` + `tabbar`；列表 reactive data 用 `where: { limit:10, page:1 }` 分页 |
| `details.vue` | `pages/customer/list/details.vue` | 必须处理 `isWxWorkEnv` 分支，详见 [`multi-platform-check`](../multi-platform-check/SKILL.md) |
| `add.vue` | `pages/customer/list/addCustomer.vue` | 表单提交后调 `<module>CreateApi`，成功后 `uni.navigateBack()` |
| `search.vue`（可选） | `pages/customer/list/search.vue` | 仅当输入要求里"需要搜索页" = 是 |
| `components/` | `pages/customer/list/components/` | 至少建 `<module>ListItem.vue` 拆出列表项 |

### 3. 注册 pages.json 路由

**重要**：`pages.json` 已有近千行，禁止整个重写。使用 Edit 工具**在合适位置追加**新模块的 entry 块（建议追加到现有同类模块附近，比如 `pages/customer/*` 之后）。

每个新页面追加形如：

```jsonc
{
  "path": "pages/<module>/index",
  "style": {
    "navigationBarTitleText": "<中文名>",
    "enablePullDownRefresh": true
  }
},
```

注意事项：
- 如果输入"平台限制" = 仅 H5，外面包裹 `// #ifdef H5` / `// #endif`
- 详情页通常 `"enablePullDownRefresh": false`，列表页通常 `true`
- 顶导若自定义则加 `"navigationStyle": "custom"`

详细规则见 [`generate-page`](../generate-page/SKILL.md) 的"pages.json 注册规则"段。

### 4. 多端兼容自检

生成完成后，**必须**对照 [`multi-platform-check`](../multi-platform-check/SKILL.md) 的清单跑一遍：
- 是否漏了 `// #ifdef` 包裹？
- 是否在非企微环境裸调了 `@wecom/jssdk`？
- 详情页是否处理了 `isWxWorkEnv` 分支？

### 5. 输出报告

完成后向用户输出：

```
已生成 <模块中文名>（<module>）CRUD 脚手架：

API:
- api/<module>.ts（5 个函数）

页面:
- pages/<module>/index.vue
- pages/<module>/details.vue
- pages/<module>/add.vue
- pages/<module>/search.vue（如生成）

路由:
- pages.json 新增 N 项注册

⚠️ 需人工 review:
1. 字段映射：模板是按 customer 抄的，请核对 <module> 的字段名/类型
2. 列表项：components/<module>ListItem.vue 的展示字段
3. 表单校验规则：add.vue 中的必填项
4. 权限：是否需要在 store/getters 加按钮权限判断
```

## 红线（禁止行为）

- ❌ 禁止把 `pages.json` 整个重写——只能 Edit 局部追加
- ❌ 禁止直接 `git add` / `git commit` 生成的文件，必须留给用户 review 后自己提交
- ❌ 禁止跳过"输入要求"列出的字段，缺信息就追问用户
- ❌ 禁止把 `api/<module>.ts` 的函数命名成 `getXxx` / `fetchXxx` 等其它风格，必须 `xxxApi` 后缀
- ❌ 禁止使用 Options API 写页面，必须 `<script setup lang="ts">`
