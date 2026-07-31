---
name: generate-component
description: 在 components/ 目录下生成 Vue 组件，支持基础展示组件、表单组件、列表项组件，提供 props/emits/插槽/生命周期等完整脚手架。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "生成一个组件"
    - "新建一个组件"
    - "做一个 xx 组件"
    - "封装一个可复用的 xx"
    - "写个列表项组件"
---

# generate-component

## 何时触发

用户说"生成一个组件"、"新建一个组件"、"做一个 xx 组件"、"封装一个可复用的 xx"、"写个列表项组件"。

> 如果用户需要**完整业务模块**（页面 + API + 路由），用 [`generate-crud-module`](../generate-crud-module/SKILL.md)。如果只是生成单页，用 [`generate-page`](../generate-page/SKILL.md)。

## 项目组件结构速查

- **基础组件目录**：`components/` 或 `components/<module>/`
- **组件引用约定**：自动导入（`unplugin-auto-import`）或手动 `import Xxx from '@/components/xxx'`
- **样式隔离**：Vue SFC 默认 scoped，无需手动加 hash

参照已有组件：
- `components/oaForm/index.vue` — 表单组件（含 oaForm 使用示例）
- `components/multiplePicker/index.vue` — 选择器组件
- `pages/users/examine/components/examineListItem.vue` — 列表项组件
- `pages/users/examine/components/examineListDefault.vue` — 默认列表组件

## 输入要求

| 项 | 说明 | 示例 |
|---|---|---|
| 组件名 | PascalCase，如 `OrderCard` | `OrderCard` |
| 组件路径 | components 下的路径 | `components/order/OrderCard.vue` |
| 组件类型 | display / form / list-item / layout | list-item |
| 功能描述 | 组件用途 | "展示订单卡片，含金额、状态、操作按钮" |
| props | 传入的 Props 清单 | `{ order: OrderInfo }` |
| emits | 触发的事件清单 | `['click', 'delete']` |
| 是否需要插槽 | 是 / 否（slot / scopedSlots） | 是 |
| 平台限制 | 全平台 / H5 / APP | 全平台 |

## 组件类型模板

### 1. 展示组件（display）

```vue
<script setup lang="ts">
/**
 * OrderCard - 订单卡片展示组件
 */
interface Props {
  order: {
    id: number;
    orderNo: string;
    amount: number;
    status: number;
    createdAt: string;
  };
  showActions?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showActions: true
});

const emit = defineEmits<{
  click: [order: Props['order']];
  delete: [id: number];
}>();

const statusMap: Record<number, string> = {
  1: '进行中',
  2: '已完成',
  3: '已取消'
};

const handleClick = () => {
  emit('click', props.order);
};

const handleDelete = () => {
  emit('delete', props.order.id);
};
</script>

<template>
  <view class="order-card" @click="handleClick">
    <view class="order-card__header">
      <text class="order-card__no">{{ order.orderNo }}</text>
      <text class="order-card__status">{{ statusMap[order.status] }}</text>
    </view>
    <view class="order-card__body">
      <text class="order-card__amount">¥{{ order.amount }}</text>
      <text class="order-card__date">{{ order.createdAt }}</text>
    </view>
    <view v-if="showActions" class="order-card__footer">
      <button size="mini" @click.stop="handleDelete">删除</button>
    </view>
  </view>
</template>

<style lang="scss" scoped>
.order-card {
  padding: 24rpx;
  background: #fff;
  border-radius: 12rpx;

  &__header {
    @include flex-between;
  }

  &__no {
    font-size: 28rpx;
    color: #333;
  }

  &__status {
    font-size: 24rpx;
    color: #1890ff;
  }

  &__body {
    @include flex-between;
    margin-top: 16rpx;
  }

  &__amount {
    font-size: 32rpx;
    font-weight: bold;
    color: #f5222d;
  }

  &__footer {
    margin-top: 16rpx;
    text-align: right;
  }
}
</style>
```

### 2. 表单组件（form）

```vue
<script setup lang="ts">
/**
 * OrderForm - 订单表单组件
 */
interface Props {
  modelValue: {
    customerName: string;
    amount: number;
    remark: string;
  };
  readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  readonly: false
});

const emit = defineEmits<{
  'update:modelValue': [value: Props['modelValue']];
  'submit': [];
}>();

const formData = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});

const rules = {
  customerName: [{ required: true, message: '请输入客户名称' }],
  amount: [{ required: true, message: '请输入金额' }]
};

const handleSubmit = () => {
  emit('submit');
};
</script>

<template>
  <view class="order-form">
    <oaForm
      v-model="formData"
      :list="formConfig"
      :readonly="readonly"
      :rules="rules"
    />
    <view v-if="!readonly" class="order-form__submit">
      <button type="primary" @click="handleSubmit">提交</button>
    </view>
  </view>
</template>

<style lang="scss" scoped>
.order-form {
  &__submit {
    padding: 32rpx;
  }
}
</style>
```

### 3. 列表项组件（list-item）

```vue
<script setup lang="ts">
/**
 * CustomerListItem - 客户列表项组件
 */
interface Props {
  item: {
    id: number;
    name: string;
    phone: string;
    level: number;
    lastContactTime?: string;
  };
  isLast?: boolean;
}

withDefaults(defineProps<Props>(), {
  isLast: false
});

const emit = defineEmits<{
  click: [id: number];
}>();

const levelMap: Record<number, string> = {
  1: 'A',
  2: 'B',
  3: 'C'
};

const handleClick = () => {
  emit('click', props.item.id);
};

// 注意：setup 中需要访问 props.item
const props = defineProps<Props>();
</script>

<template>
  <view
    class="customer-item"
    :class="{ 'customer-item--last': isLast }"
    @click="handleClick"
  >
    <view class="customer-item__left">
      <view class="customer-item__name">{{ item.name }}</view>
      <view class="customer-item__phone">{{ item.phone }}</view>
    </view>
    <view class="customer-item__right">
      <text class="customer-item__level">等级 {{ levelMap[item.level] }}</text>
      <text v-if="item.lastContactTime" class="customer-item__time">
        {{ item.lastContactTime }}
      </text>
    </view>
  </view>
</template>

<style lang="scss" scoped>
.customer-item {
  @include flex-between;
  padding: 24rpx 32rpx;
  background: #fff;

  &--last {
    border-bottom: none;
  }

  &__left {
    flex: 1;
  }

  &__name {
    font-size: 32rpx;
    color: #333;
    font-weight: 500;
  }

  &__phone {
    margin-top: 8rpx;
    font-size: 26rpx;
    color: #999;
  }

  &__right {
    text-align: right;
  }

  &__level {
    font-size: 24rpx;
    color: #1890ff;
  }

  &__time {
    display: block;
    margin-top: 8rpx;
    font-size: 24rpx;
    color: #999;
  }
}
</style>
```

## 执行步骤

1. **追问缺失输入**：确认组件名、类型、功能描述
2. **选择模板**：按组件类型选择上述模板
3. **创建目录**（如需要）：
   ```bash
   mkdir -p components/<module>
   ```
4. **生成组件文件**：按模板生成，调整字段和逻辑
5. **注册组件**（如需要）：
   - 全局组件：在 `main.ts` 或 `uni.scss` 中配置
   - 局部组件：`import Xxx from '@/components/xxx'`

## 输出报告

```
已生成组件：

components/order/OrderCard.vue
├── 类型：display
├── Props：order, showActions
├── Emits：click, delete
└── 插槽：无

使用方式：
<order-card :order="order" @click="onCardClick" />

⚠️ 需人工确认：
1. Props 类型定义是否与实际数据匹配
2. 样式是否符合设计稿（如颜色、间距）
3. 是否需要注册为全局组件
4. 是否需要添加单元测试（可用 generate-test skill）
```

## 红线

- ❌ 禁止使用 Options API，必须 `<script setup lang="ts">`
- ❌ 禁止在组件内硬编码颜色，必须用 SCSS 变量或 `uni.scss` 变量
- ❌ 禁止用 `v-show` 替代 `v-if` 来控制条件渲染（除非是需要频繁切换）
- ❌ 禁止省略 `defineProps` 和 `defineEmits` 的泛型参数
- ❌ 禁止在样式中使用 `!important`
