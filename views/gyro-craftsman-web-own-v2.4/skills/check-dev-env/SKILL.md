---
name: check-dev-env
description: 拉起本项目后体检本地开发环境——Node/npm 版本、依赖是否安装、包管理器是否混用（npm vs pnpm 锁文件并存）、patch-package/husky 是否生效、node_modules/.cache 是否需清理、vue.config.js 反向代理是否指向正确后端、dev 端口与 env。输出体检清单 + 修复建议，改动前必须征得用户同意。当用户说"项目跑不起来/启动报错/装好依赖了吗/配一下代理/检查开发环境/换后端地址"时触发。
---

# check-dev-env

新接手或拉新分支后，对本地开发环境做一次体检，定位"装没装、用哪个包管理器、代理指哪、缓存要不要清"这类高频卡点。

## 原则（重要）

- **默认只读诊断**：先逐项检查、输出体检报告，**不要**擅自 `install` / 删缓存 / 改 `vue.config.js`。
- **改动需明确同意**：安装依赖、删除 `node_modules/.cache`、修改代理地址等动作，逐项征得用户确认后再执行。
- **代理地址绝不替用户提交**：`vue.config.js` 的 `devServer.proxy.target` 是个人/环境相关，只提示用户本地改、**不要 commit**（默认值 `http://dev.oa.crmeb.net` 是团队共享 dev，别覆盖）。

## 体检清单（逐项跑）

### 1. 运行时版本
- 要求（`package.json > engines`）：**Node ≥ 18，npm ≥ 8.19.2**。
- 跑 `node -v` / `npm -v` 比对；低于要求提示用户升级（项目用到的部分依赖对 Node 版本敏感）。

### 2. 包管理器（本项目有坑，重点查）
- 仓库**同时存在** `package-lock.json` 与 `pnpm-lock.yaml`，且有 `pnpm-workspace.yaml`、`packageManager` 字段为 `null` —— 说明正处于 **npm → pnpm 迁移中**。
- **必须先和用户确认用哪个**，全程只用一个，**禁止混用**（混用会让 `node_modules` 结构和锁文件打架）：
  - 选 pnpm：`pnpm install`，遵循 `pnpm-workspace.yaml` 的 `allowBuilds`。
  - 选 npm：`npm install`，依据 `package-lock.json`。
- 提示：`.mcp.json`、`pnpm-lock.yaml`、`pnpm-workspace.yaml` 当前是未跟踪文件，是否纳入版本控制由用户/团队决定，本 skill 不代为 `git add`。

### 3. 依赖是否安装
- 检查 `node_modules` 是否存在、是否与锁文件一致（如刚切分支、锁文件变更）。
- 缺失或不一致 → 征得同意后用**第 2 步选定的包管理器**安装。

### 4. postinstall 链（patch-package / husky）
- `package.json > scripts.postinstall = "patch-package"`：安装后会按 `patches/` 打补丁。
- 若用户手动跳过了 postinstall、或补丁未生效（运行时行为与未打补丁一致），提示重跑安装或手动 `npx patch-package`。
- 项目带 husky git hooks，安装后应自动装好；hooks 没生效时提示重装。

### 5. node_modules/.cache（构建缓存异常）
- 该目录为旧工具链的构建缓存。出现页面白屏/组件不更新/HMR 异常且代码本身无误时，多为缓存陈旧导致。
- 现象匹配时，提示用户：**删除 `node_modules/.cache` 并重启 dev server**。删除前先确认。

### 6. 反向代理（vue.config.js）
- `devServer.proxy` 三条：`^/api`、`^/uploads`、`^/ws(ws:true)`，默认 `target: http://dev.oa.crmeb.net`，`changeOrigin: true`。
- 询问用户后端要指向哪：
  - 用团队共享 dev → 保持默认，无需改。
  - 指向本地/自有后端 → 提示用户在**本地**把对应 `target` 改成自己的地址，注意 `^/ws` 这条要保留 `ws: true`（WebSocket）。
- 强调：**该修改不要提交**，避免污染他人环境。

### 7. dev 端口与环境变量
- dev 端口默认 **9527**（`port || npm_config_port || 9527`）；被占用时用 `npm_config_port`/环境变量 `port` 改，或释放端口。
- env：仓库仅有 `.env.production`，开发态走默认值；如用户需要自定义开发变量，提示新增 `.env.development`（不要把私有地址写进会提交的文件）。

### 8. 启动
- 体检通过后：`npm run dev`（或 `pnpm dev`）= `vue-cli-service serve`。
- 访问地址带前缀 `roterPre`（`vue.config.js` 里 `before` 钩子把 `/` 重定向到 `roterPre`），根路径会自动跳转，无需手动拼。

## 输出格式

给用户一张逐项的 ✅/⚠️/❌ 体检表 + 每个 ⚠️/❌ 的"建议动作"，把需要执行命令或改文件的项标出来，等用户点头再动手。

## 不要做的事

- 不要在未确认包管理器前就 `install`，更不要 npm 和 pnpm 都跑一遍。
- 不要替用户修改并提交 `vue.config.js` 的代理 `target`。
- 不要未经同意删除 `node_modules/.cache` 或 `node_modules`。
- 不要把私有后端地址/端口写进会被提交的文件。
- 不要凭"应该装好了"下结论，逐项实际检查后再报告。
