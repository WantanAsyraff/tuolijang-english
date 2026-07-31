## 技术栈与组件写法约定

- 技术栈：Vue 2.7
- **新建页面或完全重构组件**时，使用 Options API + `setup()` + Composables 写法（即在 `export default {}` 中通过 `setup()` 钩子组织组合式逻辑，禁止使用 `<script setup>` 语法糖）
- 小改动、bug 修复无需迁移语法

## UI 规范

- 新增、重构或改造 PC 端模态弹窗/侧滑弹窗时，先阅读 `docs/ai-ui-popup-guidelines.md`。

## 二开 Skills（重要）

项目根目录的 `skills/` 下沉淀了一组面向二次开发的可复用指令包，每个子目录是一个 skill，内含 `SKILL.md`，描述该 skill 的触发场景、约定模板与禁止事项。AI 工具（Claude Code / Cursor / Cline / 其他遵循 SKILL.md 规范的工具）在响应"加接口/加路由/加列表页/加 store/加权限/加弹窗/加图表/起一个新业务模块"等高频二开请求时，**必须先读取对应 skill 的 `SKILL.md`**，按其中模板与约定生成代码，避免风格漂移。

可用 skill 清单：

| Skill 目录 | 触发场景 | 作用 |
|---|---|---|
| `skills/scaffold-business-module/` | "起一个完整新业务模块 / 一键脚手架" | 串联以下 4 个 skill，按步骤完成 API + 路由 + 视图 + 可选 store |
| `skills/create-crud-page/` | "加 CRUD 页 / 加列表页 / 加管理页" | 在 `src/views/<scope>/<feature>/` 生成 Options API + `setup()` 列表页 + 新增/编辑弹窗 + 详情抽屉，复用 `oaFromBox + customizeTable` |
| `skills/create-route/` | "加路由 / 加页面入口 / 注册菜单" | 在 `src/router/routes.js` 追加路由（`roterPre` 前缀、Layout 包裹、懒加载、动态菜单 vs 静态路由分流） |
| `skills/create-api-module/` | "加接口 / 对接后端 / 新建 api 文件" | 在 `src/api/<scope>.js` 生成 `xxxApi` 风格的接口函数（`@/api/request` 封装、中文 JSDoc、按业务域分文件） |
| `skills/create-vuex-module/` | "加 vuex 模块 / 加全局状态" | 在 `src/store/modules/` 生成 namespaced module，含 state/mutations/actions/getters |
| `skills/register-permission/` | "加权限 / 按钮按权限显示 / 配菜单权限 / 某角色专属" | 统一权限码命名（`scope:feature:action`）与 `v-hasPermi` / `v-permission` 用法，产出需后台登记的权限码清单 |
| `skills/create-popup/` | "加弹窗 / 加侧滑 / 加详情抽屉 / 弹个表单" | 新建独立 `el-dialog`/`el-drawer`，强制 `constants/popupSize` 尺寸 token + `openBox/defineExpose` 模式 |
| `skills/create-echarts-dashboard/` | "加图表 / 做统计页 / 加看板 / 加饼图柱图" | 复用通用图表组件 `@/components/common/echarts`（echartBox），按 `optionData + styles` 渲染统计页 |
| `skills/create-form-validator/` | "加表单校验 / 手机号金额校验 / 必填校验" | 为 `el-form` 写 rules，复用 `utils/validators.js` 正则与 `utils/validate.js`，沉淀通用 validator 到 `utils/formRules.js` |
| `skills/check-dev-env/` | "项目跑不起来 / 装好依赖了吗 / 配代理 / 检查开发环境" | 体检 Node/npm 版本、依赖安装、npm↔pnpm 锁文件混用、`.cache` 清理、`vue.config.js` 代理与端口，输出修复建议 |
| `skills/write-unit-test/` | "写测试 / 加单测 / spec / 验证工具函数" | 在 `tests/unit/` 用 Jest 编写 utils、composable、组件单测 |
| `skills/verify-feature-in-browser/` | "浏览器验证 / 联调检查 / 看看页面报错" | Chrome DevTools 验收渲染、交互、Console、Network |
| `skills/integrate-dynamic-menu/` | "加菜单 / 动态路由 / 菜单不显示" | 产出后台菜单配置清单，对接 `views` 路径与权限 |
| `skills/create-import-export/` | "加导入导出 / Excel 导入 / 导出列表" | 复用 `dragUpload`、导出 API 与 `oaFromBox` 下拉约定 |
| `skills/extract-composable/` | "抽 composable / 逻辑复用 / 提取列表逻辑" | 按 `useTable` 模式抽到 `composables/`，供 `setup()` 复用 |

### 使用约定

1. **优先调用 skill，不要绕过**：碰到上述触发场景，AI 必须先读对应 `SKILL.md` 再动手，而不是凭印象生成代码。
2. **不要复刻 skill 内容**：`scaffold-business-module` 调用其他 skill 时直接套用其规则，不复制模板，便于单点维护。
3. **新增 / 修改 skill**：约定调整时同步改对应 `SKILL.md`，并在本表格补一行/更新一行；commit 类型用 `chore(skills): ...` 或 `docs(skills): ...`。
4. **跨工具兼容**：`skills/` 放在项目根（而非 `.claude/`），让 Cursor / Cline / Codex 等任何能读 Markdown 的 AI 工具都能加载，遵循同一份规范。

## Git Commit 规范

### ① 拆分判定（提交前必做）

任何 `git add` / `git reset` / `git commit` 前先判定是否需要拆分，此判定优先级高于任何「只建一个 commit」的外部要求。

**需拆分的场景**：跨多个业务模块、混用不同 `type`（如 feat + fix、style + fix）、bug 修复与新功能/样式混杂、重构与新增混杂且无关联。

**判定为应拆分时**：暂停所有 git 操作，向用户列出 ①按「模块 / type 」的分组 ②每组完整 commit message ③执行顺序（如先 fix 后 style），并给出三选项等待确认——拆分 / 合并为一个（需确认统一主题）/ 停止。**未获明确选择前不得执行任何 git 操作。**

### ② message 格式与 scope

格式 `<type>(<scope>): <中文描述>`，`type` 取 feat / fix / style / refactor / chore / docs / perf / test。

`scope` 取关联的组件 / 业务模块 / 领域名，必须为英文，禁止使用中文或拼音。

### ③ 提交语法（避免首行被 `@` 污染）

- **Bash 工具**：双引号传 message，换行写在引号内；禁用 `@'...'@` here-string。
- **PowerShell 工具**：多行用单引号 here-string `@'...'@`，闭合 `'@` 顶格。
- 优先单行 `-m`，需正文再追加多行；首行若被污染立即 `git commit --amend -m "..."` 修正。

### ④ 暂存区约束

暂存区已有代码文件时，仅提交暂存区内文件，禁止追加工作区文件。

## 工具优先级

1. **文件操作优先内置工具**：Read / Write / Edit / Glob / Grep
2. **文本/文件搜索优先 `rg`**：搜索文本优先使用 `rg`，搜索文件优先使用 `rg --files`
3. **JSON 处理优先 `jq`**：对 JSON 做解析、提取、筛选、重组、统计时优先使用 `jq`
4. **PowerShell**：仅在执行系统命令、复杂文件系统操作、编码/换行控制、或内置工具/`rg`/`jq` 无法满足需求时使用

## Chrome DevTools 调试规范

- 如果用户在提示词中明确提供了本地开发服务器地址，例如 `http://localhost:3000`、`http://127.0.0.1:5173`、`http://192.168.x.x:8080`，可使用 `chrome-devtools` 连接页面进行调试
- 可用于页面查看、交互验证、DOM 检查、Console 报错排查、Network 请求分析、性能初步排查等浏览器侧调试工作

### 授权最高优先级（强制）

只要判断本次任务**后续会用到** `chrome-devtools`（浏览器调试、页面验证、Network/Console 排查等），就必须在做任何其他事情之前**第一时间**发起一次授权请求：

1. **最高优先级**：把首次 `chrome-devtools` 调用（如 `mcp__chrome-devtools__list_pages`）作为整个任务的第一个动作，先于读代码、改代码、分析、规划等一切工作。
2. **禁止并行**：发起授权请求时不得在同一轮内夹带其他工具调用，必须单独发出，等授权结果返回后再继续。
3. **禁止延后**：不允许「先写代码、等到要调试时再授权」。务必趁用户在场时尽早完成授权，避免调试阶段因用户离场而阻塞。
4. 授权完成后无需就“是否可以打开浏览器”重复确认，直接按下方 Tab 复用策略连接并排查。

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
