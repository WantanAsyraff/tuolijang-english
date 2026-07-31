---
name: skills-index
description: 项目自定义 skills 的总入口与使用说明
---

# skills/

这里收录了陀螺匠 OA 移动端项目的**二次开发脚手架 skill**，旨在让 AI 编程助手（Claude Code / Cursor / Copilot / Codex 等）按统一规范完成高频重复工作。

## 设计原则

1. **单一事实源**：每个 skill 一个目录，主文件为 `SKILL.md`，包含触发条件、执行步骤、参考模板路径。其他 AI 工具配置（`.cursorrules` / `AGENTS.md` / `CLAUDE.md`）只做"索引 + 引用"，不复制内容。
2. **参照项目实存**：SKILL.md 不内嵌大段代码模板，而是指向 `pages/customer/` 等真实文件作为参考，避免模板与实际代码漂移。
3. **生成后必须人工 review**：所有 skill 生成的代码都需要开发者复核（命名、字段、业务逻辑），AI 不直接 commit。

## 可用 Skills

| 名称 | 触发场景 | 路径 |
|---|---|---|
| `generate-crud-module` | 新增完整业务模块（API + 列表 + 详情 + 表单 + 路由） | [generate-crud-module/SKILL.md](./generate-crud-module/SKILL.md) |
| `generate-page` | 单独新建一个页面并注册路由 | [generate-page/SKILL.md](./generate-page/SKILL.md) |
| `generate-api` | 批量生成 `api/<module>.ts` 中的请求函数 | [generate-api/SKILL.md](./generate-api/SKILL.md) |
| `multi-platform-check` | H5 / APP / 企业微信三端兼容性审查 | [multi-platform-check/SKILL.md](./multi-platform-check/SKILL.md) |
| `generate-store-module` | 生成 Vuex 模块 + 同步类型与注册 | [generate-store-module/SKILL.md](./generate-store-module/SKILL.md) |

## 二开脚手架 Skills（补充）

| 名称 | 触发场景 | 路径 |
|---|---|---|
| `generate-router` | 注册新路由、配置导航栏、tabbar 关联 | [generate-router/SKILL.md](./generate-router/SKILL.md) |
| `generate-component` | 生成 Vue 组件（展示/表单/列表项/布局） | [generate-component/SKILL.md](./generate-component/SKILL.md) |
| `generate-composable` | 生成组合式函数（状态管理/业务逻辑封装） | [generate-composable/SKILL.md](./generate-composable/SKILL.md) |
| `generate-test` | 生成单元测试文件（Vitest API/Composable/工具函数） | [generate-test/SKILL.md](./generate-test/SKILL.md) |
| `generate-constants` | 生成枚举常量、状态码、配置常量 | [generate-constants/SKILL.md](./generate-constants/SKILL.md) |

## 如何被各 AI 工具识别

- **Claude Code**：本目录在根目录而非 `.claude/skills/`，不会被自动注册为 Skill 工具调用。模型通过 `CLAUDE.md → AGENTS.md → 本索引` 链路看到 skill 清单，按用户意图主动读取对应 SKILL.md。
- **Cursor / Codex CLI**：通过 `AGENTS.md` 的 "可用 Skills" 段落知道入口。
- **手动使用**：开发者也可直接打开 SKILL.md 阅读步骤、抄写命令。
