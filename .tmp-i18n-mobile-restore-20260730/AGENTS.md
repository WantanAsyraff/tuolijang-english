## 项目开发规范

- 技术栈：Vue 3 / UniApp
- 使用 `<script setup>` + Composables 写法

## 可用 Skills（二次开发脚手架）

项目根目录 `skills/` 下提供了 5 个二开 skill，针对高频重复工作。**收到对应意图时，必须先阅读对应 SKILL.md 再执行**，禁止凭记忆生成代码。

| 触发意图（用户说法示例） | 调用 Skill | 入口文件 |
|---|---|---|
| "新建一个 xx 业务模块"、"照着 customer 做一个 xx"、"生成 xx 的 CRUD" | `generate-crud-module` | [skills/generate-crud-module/SKILL.md](./skills/generate-crud-module/SKILL.md) |
| "新建一个页面"、"加一个 xx 页面"、"新增路由" | `generate-page` | [skills/generate-page/SKILL.md](./skills/generate-page/SKILL.md) |
| "加几个接口"、"对接 xx 接口"、"把 swagger 转成 api 函数" | `generate-api` | [skills/generate-api/SKILL.md](./skills/generate-api/SKILL.md) |
| "检查多端兼容"、"看下企微环境能不能跑"、"三端兼容审查" | `multi-platform-check` | [skills/multi-platform-check/SKILL.md](./skills/multi-platform-check/SKILL.md) |
| "新建一个 store 模块"、"加一个 vuex 模块"、"把 xx 状态拆出 app.ts" | `generate-store-module` | [skills/generate-store-module/SKILL.md](./skills/generate-store-module/SKILL.md) |

通用约束：

- skill 生成的代码默认**不自动 commit**，必须让用户 review；commit 时仍走本文档"Git Commit 规范"
- 每个 SKILL.md 末尾的"红线"段是硬约束，禁止越界
- skill 之间可以相互调用（如 `generate-crud-module` 收尾会调 `multi-platform-check`）

## Git Commit 规范

提交信息使用 Conventional Commits 格式：

- 格式：`<type>(<scope>): <description>`
- `type` 和 `scope` 使用英文（如 feat, fix, style, refactor, chore, docs, perf, test）
- `scope` 为模块/领域名称，使用英文（如 customer, opportunity, approval, users）
- `description` 正文使用中文
- 示例：
  - `feat(customer): 新增客户批量导入功能`
  - `fix(approval): 修复审批流程中状态未更新的问题`
  - `style(opportunity): 隐藏右下角悬浮添加按钮`
  - `refactor(app): 重构入口文件，拆分启动编排与服务层`

如果暂存区中存在代码文件，则在提交代码时仅提交暂存区中的文件，禁止添加工作区中的文件。

### 多 commit 拆分策略（重要）

在执行 commit 前，必须先分析暂存区中的所有文件是否属于同一模块、同一变更主题，判断是否适合放在同一个 commit 中。

**需要拆分的典型场景**：

- 涉及多个不同业务模块（如同时改动 customer 和 opportunity）
- 同时包含不同 type 的改动（如 feat + fix + docs 混杂）
- 一个独立 bug 修复与一个新功能混在一起
- 重构与功能新增混在一起

**判定不适合放在同一个 commit 时的处理流程**：

1. 暂停 commit 操作，**禁止**直接合并提交
2. 向用户输出拆分建议，明确列出：
   - 当前暂存区文件按主题/模块归类后的分组
   - 每个分组建议的 commit message（遵循 Conventional Commits 格式）
   - 拆分后的执行顺序（如先 fix 后 feat）
3. 提供以下三个选项，等待用户明确指令：
   - **拆分成多个 commit**：按建议的分组依次执行 `git reset` → 分批 `git add` → 分批 `git commit`
   - **不拆分**：保留当前暂存区文件，合并为一个 commit（需用户给出统一的 commit message 主题）
   - **停止 commit**：终止本次提交操作，不做任何 git 变更

在收到用户的明确选择前，**不得擅自执行任何 `git add`、`git reset`、`git commit` 操作**。

## 工具优先级

1. **文件操作优先内置工具**：Read / Write / Edit / Glob / Grep
2. **文本/文件搜索优先 `rg`**：搜索文本优先使用 `rg`，搜索文件优先使用 `rg --files`
3. **JSON 处理优先 `jq`**：对 JSON 做解析、提取、筛选、重组、统计时优先使用 `jq`
4. **PowerShell**：仅在执行系统命令、复杂文件系统操作、编码/换行控制、或内置工具/`rg`/`jq` 无法满足需求时使用

## Chrome DevTools 调试规范

- 如果用户在提示词中明确提供了本地开发服务器地址，例如 `http://localhost:3000`、`http://127.0.0.1:5173`、`http://192.168.x.x:8080`，可以直接使用 `chrome-devtools` 连接页面并进行调试
- 可用于页面查看、交互验证、DOM 检查、Console 报错排查、Network 请求分析、性能初步排查等浏览器侧调试工作
- 若用户已经提供可访问的本地 URL，无需额外追问“是否可以打开浏览器”这一类确认问题，直接连接并开始排查
- 若提供的地址不是本地开发地址，或页面无法访问，再根据实际情况补充确认

### Tab 复用策略（重要）

收到用户提供的 URL 后，**禁止**直接调用 `mcp__chrome-devtools__new_page` 新开页面，必须先复用已有 Tab：

1. 调用 `mcp__chrome-devtools__list_pages` 查看当前所有已打开的 Tab
2. 在返回结果中**按 URL 精确匹配或同源 + 路径前缀匹配**已有的 Tab
   - 命中：调用 `mcp__chrome-devtools__select_page` 切换到该 Tab 后直接进行后续调试操作；如目标路径与当前 URL 不一致，可在该 Tab 上调用 `mcp__chrome-devtools__navigate_page` 跳转到目标 URL，避免新开
   - 未命中：才调用 `mcp__chrome-devtools__new_page` 在新 Tab 中打开目标 URL
3. 避免重复开启同一站点的多个 Tab，减少端口/会话残留对调试结果的干扰

## 蓝湖 MCP 使用规范

### 链接校验（重要）

调用 `mcp__lanhu__lanhu_design` 前必须先校验用户提供的 URL：

- ✅ **stage 页面**（完整画板）：URL 路径为 `/item/project/stage?tid=...&pid=...`
- ❌ **detailDetach 预览页**：URL 路径包含 `/detailDetach` 且带 `image_id=...&type=image`

如果用户提供的是 `detailDetach` 预览链接，**不要直接调用**，先让用户回到蓝湖 stage 页面复制完整链接。原因：detailDetach 返回的画板是 @0.5x 压缩预览（720×512），所有尺寸被折半甚至更小（例如 390×71 的卡片会变成 216×71 左右），推导出的像素值不可靠，会反复返工。

提示话术示例：
> 你给的是 detailDetach 预览链接，返回的尺寸会被压缩为 @0.5x，导致具体像素值推导不准。请在蓝湖打开 stage 画板视图后复制链接（URL 含 `/item/project/stage`），再发我。

### 调用参数

调用时默认携带完整 `include` 参数，保证拿到原始画板的精准尺寸：

```json
{
  "mode": "analyze",
  "include": ["html", "tokens", "layout", "layers"]
}
```

`layer_tree` 字段包含未经缩放的原始尺寸（来自 Sketch artboard），**优先以它为准**。`sketch_annotations` 里的坐标是 @0.5x 预览值，仅作定位参考。

## Apifox MCP 使用规范

调用 apifox mcp 查询接口时，**仅在 id 为 `1530621` 的 `Tuoluojiang` 项目中进行查询**，禁止跨项目读取其他项目的接口数据。

## MasterGo MCP 使用规范

### 只读原则（强制）

MasterGo MCP **仅限用于读取设计稿信息**，用于辅助代码还原与分析。严禁调用任何会修改、创建、删除设计稿内容的接口，严禁对他人维护的设计文件产生任何写入副作用。

- ✅ 允许的只读接口：`getDsl`、`getMeta`、`getComponentLink`、`getComponentGenerator`、`getD2c`、`C2d` 等只读取设计数据或生成本地代码的调用
- ❌ 禁止的操作：任何改动画板、节点、样式、组件、变量、图层名称、评论等写入类操作；即便 MCP 未来新增此类接口，也默认不调用

### 调用约束

- 只传入必要的 `fileId` + `layerId`（或短链），不得附带任何会触发写入的参数
- 如果接口返回结果提示「将修改设计稿」「已创建图层」等写入语义，立即停止并告知用户
- 设计稿的任何调整必须由设计同事在 MasterGo 客户端中手动完成，AI 不直接代劳
