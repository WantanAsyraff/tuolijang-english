---
name: generate-page
description: 新建单个 uni-app 页面文件，并在 pages.json 中追加路由注册（含 navigationStyle / 条件编译 / tabbar 关联）。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "新建一个页面"
    - "加一个 xx 页面"
    - "生成 pages/xx/yy.vue"
    - "新增路由"
---

# generate-page

## 何时触发

用户说"新建一个页面"、"加一个 xx 页面"、"生成 `pages/xxx/yyy.vue`"、"新增一个路由"。

> 如果用户要生成的是**整套 CRUD 模块**（列表 + 详情 + 表单），优先用 [`generate-crud-module`](../generate-crud-module/SKILL.md)，本 skill 只负责单页。

## 输入要求

| 项 | 示例 | 必需 |
|---|---|---|
| 页面路径 | `pages/order/refund` | ✅ |
| 中文标题 | `退款申请` | ✅ |
| 页面类型 | list / detail / form / search / dashboard / blank | ✅（决定骨架模板） |
| 是否自定义导航栏 | 是 / 否 | ✅（决定 `navigationStyle` 与 `defaultNavBar`） |
| 是否需下拉刷新 | 是 / 否 | ✅ |
| 是否需 tabbar | 是 / 否（若是，第几项 currentIndex） | 默认否 |
| 平台限制 | 全平台 / 仅 H5（`#ifdef H5`）/ 仅 WEB / 仅 APP | 默认全平台 |
| 是否需登录 | 是 / 否 | 默认是（用 `useCheckLogin`） |

缺哪项就追问，禁止用默认值"猜"。

## 页面骨架模板

按页面类型选模板，**参照真实文件**而不是凭空写：

| 类型 | 参考文件 | 关键组件 |
|---|---|---|
| list（列表） | `pages/customer/list/index.vue` | `BaseContainer` + `defaultNavBar` + tab + `formBox` + 列表 + `tabbar` |
| detail（详情） | `pages/customer/list/details.vue` | sticky-navbar + `isWxWorkEnv` 分支 |
| form（表单） | `pages/customer/list/addCustomer.vue` | `BaseContainer` + `oaForm` / `moduleForm` + 底部提交按钮 |
| search（搜索） | `pages/customer/list/search.vue` | 顶部搜索框 + 结果列表 |
| dashboard | `pages/module/dashboard.vue` | 自定义导航 + 图表区 |
| blank（空白） | 仅 `BaseContainer` 包裹 + `<script setup lang="ts">` | — |

执行前用 Read 把对应模板完整读一遍。

## pages.json 注册规则

**重要**：`pages.json` 已有近千行，**禁止整体重写**。必须用 Edit 工具定位到合适位置（同模块附近）追加。

注册块的字段对照表：

```jsonc
{
  "path": "pages/xxx/yyy",           // ← 输入"页面路径"
  "navigationStyle": "custom",        // ← 仅当"自定义导航栏" = 是
  "style": {
    "navigationBarTitleText": "中文标题",  // ← 必填
    "enablePullDownRefresh": true       // ← "下拉刷新" = 是
  }
}
```

平台限制需用条件编译包裹整个 entry：

```jsonc
// #ifdef H5
{
  "path": "pages/module/questionnaire",
  "style": { "navigationBarTitleText": "问卷调查" }
},
// #endif
```

参照 `pages.json:3-8` 的 `WEB` 块、`pages.json:65-72` 的 `H5` 块。

## 执行步骤

1. **追问缺失输入** → 凑齐表格全部 8 项
2. **Read 模板文件** → 按页面类型读对应参考文件
3. **创建 .vue 文件**：
   - 必用 `<script setup lang="ts">`
   - 顶部统一引入：`import { ref, reactive } from "vue";` `import { onLoad, onShow } from "@dcloudio/uni-app";`
   - 若需登录：`import { useCheckLogin } from "@/composables/useCheckLogin";` 并在 `onLoad` 中调用
   - 若用到接口：从 `@/api/<对应模块>` 引入对应 `xxxApi`
   - 不用 Options API、不用 mixins
4. **Edit pages.json**：
   - 定位到同模块（如 `pages/order/*`）附近的最后一项后追加
   - 若没有同模块，追加到尾部 `"globalStyle"` 上方
   - 平台条件编译块要完整闭合，**不要破坏现有 `// #ifdef` 结构**
5. **若有 tabbar 关联**：检查 `pages.json` 的 `tabBar.list`，必要时追加 tab 项（包含 `pagePath` / `text` / `iconPath` / `selectedIconPath`）
6. **输出报告**：

```
已新建页面：
- pages/xxx/yyy.vue（<类型> 骨架）
- pages.json 追加 1 项路由注册

⚠️ 需人工确认:
1. 路径是否与现有冲突（可 grep "pages/xxx/yyy" 检查）
2. 业务逻辑（API 调用、字段、跳转）需要自己填
3. 若涉及 tabbar，图标资源 iconPath 需自备
```

## 红线

- ❌ 禁止整体重写 `pages.json`
- ❌ 禁止使用 Options API
- ❌ 禁止跳过登录守卫（除非用户明确说"不需要登录"）
- ❌ 禁止把 `#ifdef` 块写错（如 `H5` 写成 `h5`，或缺 `#endif`）
- ❌ 禁止在生成的页面里硬编码颜色/尺寸（按项目规范用 SCSS 变量或 uni.scss）
