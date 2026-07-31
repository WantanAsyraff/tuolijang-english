---
name: generate-test
description: 为业务模块生成单元测试文件（Vitest），包括 API 测试、Composable 测试、组件基础测试的脚手架代码。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "生成测试文件"
    - "写一个 xx 的单元测试"
    - "给 api 加个测试"
    - "补一下这个函数的测试"
    - "单元测试"
---

# generate-test

## 何时触发

用户说"生成测试文件"、"写一个 xx 的单元测试"、"给 api 加个测试"、"补一下这个函数的测试"。

> 如果用户说"做 TDD 开发"或"先写测试再写代码"，也用本 skill。

## 项目测试环境速查

### 技术栈

- 测试框架：**Vitest**（`devDependencies.vitest`）
- 断言库：Vitest 内置 `expect`
- Mock：`vi.fn()` / `vi.mock()` / `vi.spyOn()`
- 测试文件位置：`__tests__/` 目录，与被测文件同级或放在 `tests/` 根目录
- 配置文件：`vitest.config.ts`（根目录）

### 参照文件

- `vitest.config.ts` — 确认 `include` 模式、globals 设置
- 项目中现有测试文件（如有）— 确认风格

## 输入要求

| 项 | 说明 | 示例 |
|---|---|---|
| 被测目标 | 文件路径或模块名 | `api/customer.ts`、`useCheckLogin` |
| 测试类型 | api / composable / component / util | api |
| 测试场景 | 具体要覆盖的函数或场景 | `customerListApi 正常返回`、`登录态校验逻辑` |

## 执行步骤

### 1. 确定测试文件位置

```bash
# 方案 A：与源文件同级
api/customer.ts → __tests__/api/customer.test.ts

# 方案 B：统一 tests 目录
tests/unit/api/customer.test.ts
```

按项目中已有结构选择，或新建 `__tests__/` 目录。

### 2. API 测试模板

```ts
import { describe, it, expect, vi } from 'vitest';
import { customerListApi, customerDetailApi } from '@/api/customer';
import request from '@/utils/request';

// Mock request
vi.mock('@/utils/request', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn()
  }
}));

describe('api/customer', () => {
  const mockRequest = request as ReturnType<typeof vi.fn>;

  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('customerListApi', () => {
    it('正常返回列表数据', async () => {
      const mockData = { list: [{ id: 1, name: '测试' }] };
      mockRequest.get!.mockResolvedValue(mockData);

      const result = await customerListApi({ page: 1 });

      expect(result).toEqual(mockData);
      expect(mockRequest.get).toHaveBeenCalledWith('client/customer', { page: 1 });
    });

    it('传参数时正确拼接', async () => {
      mockRequest.get!.mockResolvedValue({ list: [] });

      await customerListApi({ page: 1, limit: 20 });

      expect(mockRequest.get).toHaveBeenCalledWith(
        'client/customer',
        { page: 1, limit: 20 }
      );
    });
  });

  describe('customerDetailApi', () => {
    it('传入 id 后调用正确路径', async () => {
      mockRequest.get!.mockResolvedValue({ id: 1, name: '测试' });

      await customerDetailApi(1);

      expect(mockRequest.get).toHaveBeenCalledWith('client/customer/1');
    });
  });
});
```

### 3. Composable 测试模板

```ts
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import { createComponent, nextTick } from 'vue';

// 如果测试 useXxx hook，需要 @vue/test-utils
describe('useCheckLogin', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('未登录时返回 false', async () => {
    // 假设 useCheckLogin 从 store 读取登录态
    const { result } = renderHook(() => useCheckLogin());
    expect(result.current.isLoggedIn.value).toBe(false);
  });
});
```

### 4. Util 函数测试模板

```ts
import { describe, it, expect } from 'vitest';
import { formatDate, debounce, throttle } from '@/utils/format';

describe('utils/format', () => {
  describe('formatDate', () => {
    it('正常格式化为 YYYY-MM-DD', () => {
      const date = new Date('2024-01-15T10:30:00');
      expect(formatDate(date)).toBe('2024-01-15');
    });

    it('传入时间戳正确处理', () => {
      const timestamp = 1705286400000;
      expect(formatDate(timestamp)).toMatch(/^\d{4}-\d{2}-\d{2}$/);
    });
  });

  describe('debounce', () => {
    it('指定时间间隔内只执行最后一次', async () => {
      const fn = vi.fn();
      const debouncedFn = debounce(fn, 300);

      debouncedFn();
      debouncedFn();
      debouncedFn();

      await new Promise(resolve => setTimeout(resolve, 350));

      expect(fn).toHaveBeenCalledTimes(1);
    });
  });
});
```

### 5. 运行测试命令

```bash
# 运行单个测试文件
npx vitest run __tests__/api/customer.test.ts

# 监听模式（开发时）
npx vitest __tests__/api/customer.test.ts

# 运行全部测试
npx vitest run
```

### 6. 输出报告

```
已生成测试文件：

tests/__tests__/api/customer.test.ts
├── customerListApi
│   ├── ✓ 正常返回列表数据
│   └── ✓ 传参数时正确拼接
├── customerDetailApi
│   └── ✓ 传入 id 后调用正确路径

运行命令：
npx vitest run __tests__/api/customer.test.ts

⚠️ 需人工确认：
1. Mock 数据是否符合真实接口返回结构
2. 错误场景是否需要测试（如网络异常、401 处理）
3. 测试数据是否需要从环境变量或配置文件读取
```

## 红线

- ❌ 禁止用 `console.log` 调试测试，改为 `console.error` 或注释
- ❌ 禁止跳过必要的 mock（如 request 依赖）
- ❌ 禁止硬编码真实接口 URL，测试必须隔离
- ❌ 禁止写 `sleep(1000)` 等待异步，改用 `waitFor` 或 `nextTick`
- ❌ 禁止在生产代码中添加测试调试用的 `debugger`
