---
name: generate-composable
description: 在 composables/ 目录下生成 Vue 3 Composable 函数，支持状态管理、业务逻辑封装、API 调用封装，提供完整的 reactive/ref 封装脚手架。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "生成一个 composable"
    - "写一个 hook 函数"
    - "封装一个业务逻辑"
    - "做个 xx 的组合式函数"
    - "抽出一个 useXxx"
---

# generate-composable

## 何时触发

用户说"生成一个 composable"、"写一个 hook 函数"、"封装一个业务逻辑"、"做个 xx 的组合式函数"、"抽出一个 useXxx"。

> Composable（组合式函数）是 Vue 3 的核心模式，用于复用有状态的逻辑。本 skill 适用于需要**跨页面复用**或**状态管理**的业务逻辑抽取。

## 项目 Composable 结构速查

- **目录**：`composables/`
- **命名规范**：`use<功能名>.ts`，如 `useCheckLogin.ts`、`useFormSubmit.ts`
- **引用约定**：手动 `import { useXxx } from '@/composables/useXxx'`
- **常用 hooks**：
  - `useCheckLogin` — 登录态校验
  - `useCountDown` — 倒计时
  - `useLoadMore` — 分页加载
  - `usePicker` — 选择器封装

参照已有 composable：
- `composables/useCheckLogin.ts` — 登录态守卫
- `composables/useCountDown.ts` — 倒计时逻辑
- `composables/useLoadMore.ts` — 分页加载封装

## 输入要求

| 项 | 说明 | 示例 |
|---|---|---|
| 函数名 | use 开头 PascalCase | `useOrderList` |
| 功能描述 | 用途说明 | "封装订单列表的加载、分页、刷新逻辑" |
| 状态定义 | 需要的状态字段 | `{ list, loading, finished, page }` |
| 方法定义 | 需要的方法 | `{ loadMore, refresh, search }` |
| 依赖 API | 调用的接口 | `orderListApi` |
| 是否需要持久化 | 是 / 否 | 否 |

## Composable 模板

### 1. 列表加载型（useLoadMore 风格）

```ts
import { ref, computed } from 'vue';
import { orderListApi } from '@/api/order';
import type { OrderInfo } from '@/api/order';

/**
 * 订单列表 Composable
 * @description 封装订单列表的加载、分页、刷新逻辑
 */
export function useOrderList() {
  // 状态
  const list = ref<OrderInfo[]>([]);
  const loading = ref(false);
  const finished = ref(false);
  const page = ref(1);
  const limit = ref(10);
  const where = ref<{
    keyword?: string;
    status?: number;
  }>({});

  // 计算属性
  const isEmpty = computed(() => list.value.length === 0 && !loading.value);

  // 加载数据
  const loadData = async (reset = false) => {
    if (loading.value) return;
    if (reset) {
      page.value = 1;
      list.value = [];
      finished.value = false;
    }
    if (finished.value) return;

    loading.value = true;
    try {
      const { data } = await orderListApi({
        page: page.value,
        limit: limit.value,
        ...where.value
      });
      if (data.length < limit.value) {
        finished.value = true;
      }
      list.value.push(...data);
      page.value++;
    } catch (err) {
      console.error('加载订单列表失败', err);
      finished.value = true;
    } finally {
      loading.value = false;
    }
  };

  // 刷新
  const refresh = () => loadData(true);

  // 加载更多
  const loadMore = () => {
    if (!finished.value && !loading.value) {
      loadData();
    }
  };

  // 搜索
  const search = (params: typeof where.value) => {
    where.value = params;
    refresh();
  };

  // 重置
  const reset = () => {
    where.value = {};
    refresh();
  };

  return {
    // 状态
    list,
    loading,
    finished,
    isEmpty,
    where,
    // 方法
    loadData,
    refresh,
    loadMore,
    search,
    reset
  };
}
```

### 2. 表单提交型

```ts
import { ref, reactive } from 'vue';
import { orderCreateApi, orderUpdateApi } from '@/api/order';
import { onLoad } from '@dcloudio/uni-app';

/**
 * 订单表单 Composable
 * @description 封装订单表单的校验、提交、状态管理
 */
export function useOrderForm(id?: number) {
  const isEdit = !!id;
  const submitting = ref(false);
  const formRef = ref();

  const formData = reactive({
    customerId: '',
    customerName: '',
    amount: 0,
    remark: ''
  });

  const rules = {
    customerId: [{ required: true, message: '请选择客户' }],
    amount: [
      { required: true, message: '请输入金额' },
      { type: 'number', message: '金额必须为数字' }
    ]
  };

  // 校验
  const validate = () => {
    return new Promise<boolean>((resolve) => {
      formRef.value?.validate((valid: boolean) => {
        resolve(valid);
      });
    });
  };

  // 提交
  const submit = async () => {
    const valid = await validate();
    if (!valid) return;

    submitting.value = true;
    try {
      if (isEdit) {
        await orderUpdateApi(id!, formData);
      } else {
        await orderCreateApi(formData);
      }
      uni.showToast({ title: '保存成功' });
      uni.navigateBack();
    } catch (err) {
      console.error('提交失败', err);
    } finally {
      submitting.value = false;
    }
  };

  // 加载详情（编辑时）
  const loadDetail = async () => {
    if (!isEdit) return;
    try {
      const { data } = await orderDetailApi(id);
      Object.assign(formData, data);
    } catch (err) {
      console.error('加载详情失败', err);
    }
  };

  return {
    isEdit,
    submitting,
    formRef,
    formData,
    rules,
    validate,
    submit,
    loadDetail
  };
}
```

### 3. 工具型

```ts
import { ref, onUnmounted } from 'vue';

/**
 * 倒计时 Composable
 * @description 通用倒计时逻辑，适用于验证码倒计时、活动倒计时等
 */
export function useCountDown(initialSeconds = 60) {
  const count = ref(0);
  const isCounting = ref(false);
  let timer: ReturnType<typeof setInterval> | null = null;

  const start = (seconds = initialSeconds) => {
    if (isCounting.value) return;

    count.value = seconds;
    isCounting.value = true;

    timer = setInterval(() => {
      count.value--;
      if (count.value <= 0) {
        stop();
      }
    }, 1000);
  };

  const stop = () => {
    if (timer) {
      clearInterval(timer);
      timer = null;
    }
    isCounting.value = false;
    count.value = 0;
  };

  // 组件卸载时自动清理
  onUnmounted(() => {
    stop();
  });

  return {
    count,
    isCounting,
    start,
    stop
  };
}
```

### 4. 状态共享型（模块级状态）

```ts
import { ref, shallowRef } from 'vue';

/**
 * 模块级状态共享示例
 * @description 当多个组件需要共享同一份状态时使用
 */
const sharedState = shallowRef<{
  visible: boolean;
  data: any;
}>({
  visible: false,
  data: null
});

export function useGlobalModal() {
  const open = (data?: any) => {
    sharedState.value = {
      visible: true,
      data
    };
  };

  const close = () => {
    sharedState.value.visible = false;
  };

  return {
    state: sharedState,
    open,
    close
  };
}
```

## 执行步骤

1. **追问缺失输入**：确认函数名、功能、状态、方法
2. **选择模板**：按类型选择上述模板
3. **创建目录**（如需要）：
   ```bash
   mkdir -p composables
   ```
4. **生成文件**：按模板生成，调整字段和逻辑
5. **导出使用**：在页面中 `import { useXxx } from '@/composables/useXxx'`

## 输出报告

```
已生成 Composable：

composables/useOrderList.ts
├── 类型：列表加载型
├── 状态：list, loading, finished, page, where
├── 方法：loadData, refresh, loadMore, search, reset
└── 依赖：orderListApi

使用方式：
```ts
const { list, loading, finished, refresh, loadMore } = useOrderList();

// 初始化加载
refresh();

// 触底加载
loadMore();
```

⚠️ 需人工确认：
1. API 路径和参数是否正确
2. 类型定义是否与实际数据匹配
3. 是否需要在页面卸载时清理副作用（如 clearInterval）
```

## 红线

- ❌ 禁止使用 Options API 或 Vue 2 style，必须 `<script setup>` 或纯函数
- ❌ 禁止在 composable 中直接调用 `uni.showToast` 等 UI 方法（统一由调用方处理）
- ❌ 禁止忘记清理副作用（如 setInterval、eventListener），必须在 onUnmounted 中清理
- ❌ 禁止使用 `this` 上下文
- ❌ 禁止在 composable 中直接修改 props（只能通过 emit）
