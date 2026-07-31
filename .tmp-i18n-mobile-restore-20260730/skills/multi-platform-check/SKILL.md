---
name: multi-platform-check
description: 对 uni-app 代码做 H5 / APP / 企业微信三端兼容性审查，找出缺失的 #ifdef、误用的端专属 API、缺企微环境守卫的代码。
metadata:
  type: skill
  scope: review
  triggers:
    - "检查这段代码的多端兼容"
    - "审查 H5/APP 兼容性"
    - "看下这里在企微环境下能不能跑"
    - "做一次三端兼容审查"
---

# multi-platform-check

## 何时触发

用户说"检查这段代码的多端兼容"、"审查 H5/APP 兼容性"、"看下这里在企微环境下能不能跑"、"做一次三端兼容审查"，或者本 skill 被 [`generate-crud-module`](../generate-crud-module/SKILL.md) / [`generate-page`](../generate-page/SKILL.md) 在收尾时调用。

**本 skill 只做审查，不自动改代码**——输出问题清单 + 推荐改法，由用户决定是否应用。

## 项目三端约束速查

| 平台 | 条件编译宏 | 特点 |
|---|---|---|
| H5 浏览器 | `// #ifdef H5` | 包含企业微信 H5 |
| H5 启动页 | `// #ifdef WEB` | uni 的 web 标识，仅 pages.json launch 用 |
| iOS / Android | `// #ifdef APP-PLUS` | 用 `plus.*` API |
| 微信小程序（如启用） | `// #ifdef MP-WEIXIN` | — |
| 企业微信环境 | 运行时判断 `isWxWorkEnv`（非编译宏） | 见 `libs/wxwork.ts` |

参照真实用例：
- `pages.json:3-8` `WEB` 块；`pages.json:65-72` `H5` 块
- `utils/request.ts:46-60` `APP-PLUS` + `H5` 分支
- `pages/customer/list/details.vue:2` 顶层 `v-if="!isWxWorkEnv || ..."`

## 审查清单（按优先级）

### 🔴 P0（必须修复）

1. **裸调企微 SDK**
   - 检查：是否在非企微环境调用 `@wecom/jssdk` 或 `libs/wxwork*.ts` 暴露的方法
   - 正确做法：调用前必须有 `if (isWxWorkEnv)` 或 `v-if="isWxWorkEnv"` 守卫
   - 反例：`pages/customer/list/details.vue:31` 的 `openCustomerChat`，调用方都有 `v-if` 兜底
   - 历史教训：`b4acb54d fix(opportunity): 非企微环境下隐藏发起聊天入口` 就是漏了这个守卫

2. **端专属 API 无 `#ifdef`**
   - `plus.*` / `uni.requireNativePlugin` / `getApp()` 必须在 `// #ifdef APP-PLUS` 内
   - `window` / `document` / `location` 必须在 `// #ifdef H5` 内（uni 在 App 端没有 DOM）
   - `wx.*` 必须在 `// #ifdef MP-WEIXIN` 内

3. **pages.json 条件编译块破损**
   - `// #ifdef` 必须有对应 `// #endif`
   - JSON 块若被 `#ifdef` 包裹，前后逗号要保留正确

### 🟡 P1（强烈建议修复）

4. **uni.* API 平台差异**
   - `uni.getLocation`：H5 需 https + 用户授权，App 需 manifest 权限声明
   - `uni.chooseImage`：App 端文件路径是 `_doc/...`，H5 是 blob URL，后端上传需区分
   - `uni.downloadFile`：H5 跨域受 CORS 限制
   - `uni.openDocument`：H5 仅支持新窗口打开 URL，App 走原生预览
   - 检查到此类 API 时提示："此 API 在 X 端行为不同，建议加分支处理"

5. **跳转 URL 多端可达性**
   - 检查 `uni.navigateTo` / `uni.redirectTo` 目标页是否在 `pages.json` 中存在
   - 若目标页有 `#ifdef H5` 限定，调用方也要有相同条件编译
   - 外链 `window.open` 在 App 端无效，需用 `plus.runtime.openURL` 或 `uni.navigateTo` 到 webview 页

6. **存储与缓存**
   - `localStorage` 在 App 端不可用，统一用 `uni.setStorageSync` / `getStorageSync`
   - 已在 `utils/request.ts` 见到 `uni.getStorageSync`，对齐这种风格

### 🟢 P2（建议关注）

7. **样式与适配**
   - `rpx` 单位 H5 端按视口换算，App 端按 750 设计稿，关键尺寸建议明确单位
   - `safe-area-inset-*`：仅 H5（含企微 H5）和部分 App 设备需要
   - `position: fixed` 在 App 软键盘弹起时表现不同

8. **生命周期差异**
   - `onLoad` 参数：App 与 H5 都有 `options`，但企微 H5 路由是 hash 模式，刷新后参数行为不同
   - `onShow`：App 切后台再回前台会触发，H5 不会

## 执行步骤

1. **确认审查范围**：
   - 单个文件 → 直接 Read
   - 当前 diff → 跑 `git diff --name-only HEAD` 列出变更文件再逐个审查
   - 模块目录 → Glob 出 `.vue` `.ts` 文件再逐一处理
2. **逐文件扫描**：按"审查清单"P0 → P1 → P2 顺序找问题
3. **输出报告**（Markdown 表格）：

```
## 多端兼容审查报告

文件：pages/xxx/yyy.vue

| 级别 | 行号 | 问题 | 建议改法 |
|---|---|---|---|
| 🔴 P0 | 42 | 直接调用 `wx.openCustomerProfile` 未判断 isWxWorkEnv | 用 v-if 或 if 包裹 |
| 🟡 P1 | 88 | `uni.getLocation` 未处理 H5 用户拒绝授权 | 增加 fail 回调 |
| 🟢 P2 | 120 | 固定 `padding-top: 44px` 未考虑 safe-area | 用 safe-area-inset-top |

未发现问题的文件：N 个
```

4. **不自动改代码**：用户看完报告后明确说"按建议改"，再走 Edit。

## 红线

- ❌ 禁止自动改代码（除非用户明确授权"应用全部建议"）
- ❌ 禁止报告无关问题（如代码风格、命名建议）——本 skill 只看多端兼容
- ❌ 禁止漏报 P0：每发现一处裸调企微 SDK 必须独立成条
- ❌ 禁止用"可能"、"也许"含糊措辞：要么明确"会在 X 端报错"，要么不报
