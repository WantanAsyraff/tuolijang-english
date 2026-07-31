---
name: skills-index
description: 项目自定义 skills 的总入口与使用说明
---

# skills/

陀螺匠 OA PC 端**二次开发 skill** 集合，让 AI 助手按统一规范完成路由、CRUD、接口、测试、联调等高频工作。

## 设计原则

1. **单一事实源**：每个 skill 一个目录，主文件为 `SKILL.md`；`AGENTS.md` 只做索引引用，不复制正文。
2. **参照项目实存**：模板指向 `src/views/customer/` 等真实文件，避免与代码漂移。
3. **生成后须人工 review**：AI 不直接 commit。

## 五大类（二开路径图）

按典型二开顺序归类，AI 应按场景读取对应 `SKILL.md`：

### 1. 模块脚手架 — 从零起一个功能

| Skill | 触发场景 |
|---|---|
| [`scaffold-business-module`](./scaffold-business-module/SKILL.md) | 一键：API + 视图 + 路由/菜单 + 可选 Vuex |
| [`create-crud-page`](./create-crud-page/SKILL.md) | 单独新建列表页 + 弹窗 + 详情抽屉 |
| [`create-popup`](./create-popup/SKILL.md) | 独立弹窗 / 侧滑 / 抽屉 |
| [`create-form-validator`](./create-form-validator/SKILL.md) | 表单校验规则 |
| [`create-echarts-dashboard`](./create-echarts-dashboard/SKILL.md) | 统计页 / 图表看板 |
| [`create-import-export`](./create-import-export/SKILL.md) | 列表导入导出 Excel |
| [`extract-composable`](./extract-composable/SKILL.md) | 抽取组合式逻辑复用 |

### 2. 路由与菜单 — 页面如何被访问

| Skill | 触发场景 |
|---|---|
| [`create-route`](./create-route/SKILL.md) | 静态路由注册（`routes.js`） |
| [`integrate-dynamic-menu`](./integrate-dynamic-menu/SKILL.md) | 后台动态菜单对接（**默认方式**） |
| [`register-permission`](./register-permission/SKILL.md) | 按钮 / 菜单权限码 |

### 3. 数据层 — 接口与全局状态

| Skill | 触发场景 |
|---|---|
| [`create-api-module`](./create-api-module/SKILL.md) | 新建 / 扩展 `src/api/*.js` |
| [`create-vuex-module`](./create-vuex-module/SKILL.md) | Vuex namespaced module |

### 4. 质量保障 — 测试与联调

| Skill | 触发场景 |
|---|---|
| [`write-unit-test`](./write-unit-test/SKILL.md) | Jest 单元测试（utils / composable / 组件） |
| [`verify-feature-in-browser`](./verify-feature-in-browser/SKILL.md) | 浏览器联调验收（DevTools） |
| [`check-dev-env`](./check-dev-env/SKILL.md) | 开发环境体检 |

### 5. 推荐执行顺序（完整新模块）

```
scaffold-business-module
  ├─ create-api-module
  ├─ create-crud-page (+ create-popup / create-form-validator)
  ├─ integrate-dynamic-menu（或 create-route）
  ├─ register-permission
  ├─ create-import-export（按需）
  ├─ write-unit-test（按需）
  └─ verify-feature-in-browser
```

## 如何被各 AI 工具识别

- **Claude Code / Cursor**：通过 `CLAUDE.md → AGENTS.md → 本索引` 发现 skill 清单，按意图读取 `SKILL.md`。
- **手动使用**：直接打开对应目录下的 `SKILL.md`。

## 新增 skill 规范

1. 目录名：小写 + 连字符，如 `create-xxx`
2. 必含 YAML frontmatter：`name`、`description`（第三人称 + 触发词）
3. 同步更新本 `README.md` 与根目录 `AGENTS.md` 表格
4. Commit 类型：`chore(skills): ...` 或 `docs(skills): ...`
