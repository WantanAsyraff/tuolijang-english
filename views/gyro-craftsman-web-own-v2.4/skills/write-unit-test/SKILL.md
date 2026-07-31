---
name: write-unit-test
description: 为陀螺匠 OA 项目编写 Jest 单元测试（utils 纯函数、Vue 组件挂载、composable 逻辑）。当用户说"写测试/加单测/单元测试/spec"、"验证工具函数"、"给组件补测试"时触发。
---

# write-unit-test

为本项目（Vue 2.7 + @vue/cli-plugin-unit-jest + vue-jest）在 `tests/unit/` 下新增或扩展单元测试，优先覆盖**纯函数**与**可隔离的组合式逻辑**，避免重度依赖全局 store / 路由的集成测试。

## 项目测试基建

| 项 | 约定 |
|---|---|
| 配置文件 | `jest.config.js` |
| 测试目录 | `tests/unit/**/*.spec.js` |
| 路径别名 | `@/` → `src/`（已在 `moduleNameMapper` 配置） |
| 运行命令 | `npx vue-cli-service test:unit` 或 `npx jest`（`package.json` 未预置 `test` script，需用上述命令） |
| 覆盖率范围 | 默认收集 `src/utils/**`、`src/components/**`（见 `collectCoverageFrom`） |

参考现有用例：
- `tests/unit/utils/validate.spec.js` — 纯函数测试
- `tests/unit/components/Hamburger.spec.js` — 组件挂载测试

## 何时写哪种测试

| 场景 | 建议 | 文件位置 |
|---|---|---|
| `utils/validators.js`、`utils/validate.js` 等工具函数 | **必写**，成本低收益高 | `tests/unit/utils/<name>.spec.js` |
| `composables/useTable.js` 等组合式逻辑 | 抽离 fetch 为参数后 mock | `tests/unit/composables/<name>.spec.js` |
| 业务列表页 `index.vue` | 一般不写全量 E2E；只测提取出的纯函数 | 同上 |
| API 请求层 | **不测** `request.js` 本身；在 composable 层 mock `xxxApi` |

## 工作流程

1. **确定被测对象**：函数名、入参/出参、边界条件（空值、非法格式、极值）。
2. **选择目录**：
   - utils → `tests/unit/utils/`
   - 组件 → `tests/unit/components/`
   - composable → `tests/unit/composables/`（目录不存在则新建）
3. **编写 spec**，遵循 `describe` + `it` 结构，用例名用中文或英文均可，需表达业务含义。
4. **运行验证**：`npx vue-cli-service test:unit --testPathPattern=<文件名>`
5. **交付清单**：列出新增 spec 路径、覆盖的分支、未覆盖项（若有）。

## 模板

### 纯函数（参考 validate.spec.js）

```js
import { myValidator } from '@/utils/validators'

describe('Utils:myValidator', () => {
  it('合法手机号返回 true', () => {
    expect(myValidator('13800138000')).toBe(true)
  })

  it('空值返回 false', () => {
    expect(myValidator('')).toBe(false)
    expect(myValidator(null)).toBe(false)
  })
})
```

### Vue 组件（@vue/test-utils 1.x）

```js
import { shallowMount } from '@vue/test-utils'
import MyComponent from '@/components/common/MyComponent.vue'

describe('MyComponent', () => {
  it('渲染默认文案', () => {
    const wrapper = shallowMount(MyComponent, {
      propsData: { title: '测试' }
    })
    expect(wrapper.text()).toContain('测试')
  })
})
```

### Composable（mock API）

```js
import { useTable } from '@/views/inventory/composables/useTable'

const mockFetch = jest.fn().mockResolvedValue({
  data: { list: [{ id: 1 }], count: 1 }
})

describe('useTable', () => {
  it('初始化后拉取列表', async () => {
    const { tableData, fetchData } = useTable(mockFetch, { immediate: false })
    await fetchData()
    expect(mockFetch).toHaveBeenCalled()
    expect(tableData.value).toHaveLength(1)
  })
})
```

## 禁止事项

- 不要在单测里发真实 HTTP 请求
- 不要修改 `jest.config.js` 除非用户明确要求
- 不要为每个 CRUD 页面写快照测试（维护成本高、价值低）
- 测试文件不要放在 `src/` 下，统一放 `tests/unit/`

## 与其他 skill 的配合

- 用 [`create-crud-page`](../create-crud-page/SKILL.md) 生成页面后，仅当用户**明确要求**或为复杂 `utils` 补测时才调用本 skill
- 页面联调验证用 [`verify-feature-in-browser`](../verify-feature-in-browser/SKILL.md)，不替代单测
