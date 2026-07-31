---
name: generate-router
description: 在 pages.json 中注册新路由，支持条件编译（#ifdef H5/APP-PLUS/WEB）、自定义导航栏、tabbar 关联，并生成基础页面骨架。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "注册一个新路由"
    - "加一个页面路由"
    - "在 pages.json 加一条"
    - "配置路由的导航栏"
    - "设置 tabbar"
---

# generate-router

## 何时触发

用户说"注册一个新路由"、"加一个页面路由"、"在 pages.json 加一条"、"配置路由的导航栏"、"设置 tabbar"。

> 如果用户需要**一次性生成完整 CRUD 模块**（页面 + API + 路由），优先用 [`generate-crud-module`](../generate-crud-module/SKILL.md)。本 skill 只处理**纯路由层面**的工作。

## pages.json 结构速查

```jsonc
{
  "pages": [ /* 全平台页面 */ ],
  // #ifdef H5
  "h5": [ /* 仅 H5 页面 */ ],
  // #endif
  // #ifdef WEB
  "web": [ /* 仅 Web 启动页 */ ],
  // #endif
  "globalStyle": { /* 全局样式 */ },
  "tabBar": {
    "list": [ /* tabbar 项 */ ]
  }
}
```

参照：
- `pages.json:3-8` — `WEB` 块
- `pages.json:65-72` — `H5` 块
- `pages.json` 中 `tabBar.list` 的现有项

## 输入要求

| 项 | 说明 | 示例 |
|---|---|---|
| 页面路径 | pages 下的路径（不含扩展名） | `pages/order/refund` |
| 中文标题 | navigationBarTitleText | `退款申请` |
| 平台限制 | 全平台 / H5 / WEB / APP-PLUS | H5 |
| 自定义导航栏 | 是 / 否 | 否 |
| 下拉刷新 | 是 / 否 | 是（列表页）/ 否（详情页） |
| tabbar 关联 | 是 / 否（若是，提供 currentIndex） | 否 |
| 是否需生成页面文件 | 是 / 否 | 是 |

## 路由注册规则

### 全平台路由

```jsonc
{
  "path": "pages/order/refund",
  "style": {
    "navigationBarTitleText": "退款申请",
    "enablePullDownRefresh": false
  }
}
```

### H5 条件编译

```jsonc
// #ifdef H5
{
  "path": "pages/module/questionnaire",
  "style": {
    "navigationBarTitleText": "问卷调查",
    "enablePullDownRefresh": true
  }
},
// #endif
```

### APP-PLUS 条件编译

```jsonc
// #ifdef APP-PLUS
{
  "path": "pages/module/native-share",
  "style": {
    "navigationBarTitleText": "分享",
    "enablePullDownRefresh": false
  }
},
// #endif
```

### 自定义导航栏

```jsonc
{
  "path": "pages/order/custom",
  "style": {
    "navigationStyle": "custom",
    "navigationBarTitleText": "自定义导航"
  }
}
```

### tabbar 关联

```jsonc
{
  "path": "pages/customer/index",
  "style": {
    "navigationBarTitleText": "客户"
  }
}
```

在 `tabBar.list` 中追加：

```jsonc
{
  "pagePath": "pages/customer/index",
  "text": "客户",
  "iconPath": "static/tabbar/customer.png",
  "selectedIconPath": "static/tabbar/customer-active.png"
}
```

## 执行步骤

### 1. 确认 pages.json 结构

先用 Read 确认：
- 现有 `pages` 数组末尾在哪里
- 是否有 `#ifdef` 条件编译块
- `tabBar.list` 现有几项

### 2. 定位插入位置

| 平台 | 插入位置 |
|---|---|
| 全平台 | `pages` 数组末尾，或同类模块附近 |
| H5 | `#ifdef H5` 块内的 `pages` 数组 |
| WEB | `#ifdef WEB` 块内 |
| APP-PLUS | `#ifdef APP-PLUS` 块内 |

### 3. Edit 追加路由

使用 Edit 工具**局部追加**，禁止整体重写 pages.json。

示例追加到 `pages` 数组末尾：

```diff
  {
    "path": "pages/customer/list",
    "style": { "navigationBarTitleText": "客户列表" }
+ },
+ {
+   "path": "pages/order/refund",
+   "style": {
+     "navigationBarTitleText": "退款申请",
+     "enablePullDownRefresh": false
+   }
  }
```

### 4. tabbar 处理（如需要）

- 检查 `tabBar.list` 是否已满（一般最多 5 项）
- 确认图标文件存在，或在报告中提示用户补充

### 5. 生成基础页面文件（如需要）

按 [`generate-page`](../generate-page/SKILL.md) 规则生成 `.vue` 骨架文件。

### 6. 输出报告

```
已在 pages.json 注册路由：

pages/order/refund
├── 平台：H5
├── 导航栏：默认
├── 下拉刷新：否
└── tabbar：否

⚠️ 需人工确认：
1. 页面文件 pages/order/refund.vue 是否已创建
2. 图标资源 static/tabbar/xxx.png 是否存在
3. 是否需要在 globalStyle 中补充其他全局配置
```

## 红线

- ❌ 禁止整体重写 pages.json，只能局部 Edit 追加
- ❌ 禁止把 `#ifdef` 写成 `#IFDEF` 或 `#ifndef`（大小写敏感）
- ❌ 禁止在 H5 块内注册仅 APP 的页面
- ❌ 禁止在 tabBar 中重复注册已有 pagePath
- ❌ 禁止把 `navigationStyle: "custom"` 写成 `navigationStyle: "true"`
