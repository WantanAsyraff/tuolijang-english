---
name: generate-store-module
description: 生成新的 Vuex 模块（state/mutations/actions），并同步在 store/index.ts 追加类型字段、在 store/modules/index.ts 注册、必要时补 getters。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "新建一个 store 模块"
    - "加一个 vuex 模块"
    - "把 xx 状态拆出 app.ts"
---

# generate-store-module

## 何时触发

用户说"新建一个 store 模块"、"加一个 vuex 模块"、"把 xx 状态从 app.ts 拆出来"、"全局加一个 xxx 状态"。

> 项目目前所有业务状态都堆在 `store/modules/app.ts`（已 188 行 / 30+ 字段），新模块拆出是好事，但要注意三处同步。

## 项目 Store 结构速查

读以下文件确认：

- `store/index.ts` — 定义 `AppState` 接口 + `State` 接口（顶层 module 注册到这里）+ `createStore`
- `store/modules/index.ts` — 一个对象，键名 = module 名，值 = module 实例
- `store/modules/app.ts` — 标准模板：导出 `{ state, mutations, actions }`，**未启用 namespaced**
- `store/getters.ts` — 顶层 getters，签名 `(state: State) => state.<moduleName>.<field>`

⚠️ 注意：**当前所有模块都是非 namespaced**。新模块默认也保持非 namespaced，便于 `store.commit('setXxx')` / `store.state.<module>.xxx` 直接访问。除非用户明确要求 namespaced，否则不要开。

## 输入要求

| 项 | 说明 |
|---|---|
| 模块名 | 小写英文，如 `order` `notification`（即 `store/modules/<name>.ts`） |
| state 字段清单 | 每条含：字段名 / 类型（string/number/boolean/object/array）/ 初始值 / 中文注释 |
| 是否需要 actions | 默认 no；若用户给出"异步从接口拉取"则需要 |
| 是否需要全局 getter | 默认 no；若字段会被 `mapGetters` 引用则需要 |

## 执行步骤

### 1. 新建 `store/modules/<name>.ts`

模板（**严格对齐 `store/modules/app.ts` 风格**）：

```ts
import type { <Name>State } from "../index";

const state = (): <Name>State => ({
  // 按用户输入填字段与初始值
  fieldA: "",
  fieldB: 0,
  fieldC: []
});

const mutations = {
  setFieldA(state: <Name>State, value: string) {
    state.fieldA = value;
  },
  setFieldB(state: <Name>State, value: number) {
    state.fieldB = value;
  },
  setFieldC(state: <Name>State, value: any[]) {
    state.fieldC = value;
  }
};

const actions = {
  // 仅当用户要求异步时生成；空 actions 也保留 export
};

export default {
  state,
  mutations,
  actions
};
```

mutation 命名规范：一律 `set<FieldName>` 驼峰（首字母小写 set，紧跟字段大驼峰）。这是 `app.ts` 主流风格（`setDepSelectIds` / `setEnterpriseAuth`）。

### 2. Edit `store/index.ts` —— 追加类型和接口字段

定位到 `export interface State {` 块，追加：

```ts
export interface <Name>State {
  fieldA: string;
  fieldB: number;
  fieldC: any[];
}

export interface State {
  app: AppState;
  <name>: <Name>State;   // ← 新追加
}
```

**禁止**把已有 `AppState` 字段挪进新模块——只追加，不重构。如确需迁移 `app.ts` 的字段，先告诉用户并征得同意。

### 3. Edit `store/modules/index.ts` —— 注册模块

当前文件只有 4 行：

```ts
import app from "./app";
export default {
  app
};
```

改成：

```ts
import app from "./app";
import <name> from "./<name>";
export default {
  app,
  <name>
};
```

### 4. （可选）补 `store/getters.ts`

仅当输入"是否需要全局 getter" = 是时执行。追加：

```ts
const getters: GetterTree<State, any> = {
  token: (state: State) => state.app.token,
  isLogin: (state: State) => !!state.app.token,
  backgroundColor: (state: State) => state.app.backgroundColor,
  <fieldName>: (state: State) => state.<name>.<fieldName>,  // ← 新追加
};
```

### 5. 持久化（按需）

如果字段需要刷新后保留（如 token / userInfo），需要在 `app.ts` 的 `init` mutation 中加 `uni.getStorageSync` 读取，并在 setter 中加 `uni.setStorageSync` 写入。**这点必须显式问用户**，因为是否持久化影响 mutation 的实现。

### 6. 输出报告

```
已新建 vuex 模块 <name>：

文件:
- store/modules/<name>.ts（新建）
- store/index.ts（追加 <Name>State 接口 + State.<name> 字段）
- store/modules/index.ts（注册）
- store/getters.ts（追加 N 个 getter）（如生成）

使用方式:
- 读取: store.state.<name>.<field>  或  computed(() => useStore().state.<name>.<field>)
- 写入: store.commit('set<Field>', value)
- 全局 getter: useStore().getters.<fieldName>

⚠️ 需人工确认:
1. 字段类型是否需要细化（当前用了 any[] / object 等宽松类型）
2. 是否需要持久化到 uni storage（默认未做）
3. 是否要启用 namespaced（当前对齐 app.ts 的非 namespaced 风格）
```

## 红线

- ❌ 禁止改 `app.ts` 字段（除非用户明确要求迁移）
- ❌ 禁止启用 `namespaced: true`（除非用户要求，且要同步改全部 `store.commit` 调用方）
- ❌ 禁止用 Pinia 替换 Vuex（项目锁定 vuex@4.1.0）
- ❌ 禁止跳过 `store/index.ts` 的接口同步——只加 module 不加类型，TS 会报 `state.<name>` 不存在
- ❌ 禁止把 mutation 命名成 `updateXxx` / `changeXxx`，必须 `setXxx`
