---
name: generate-constants
description: 在 constants/ 或 utils/ 目录下生成枚举常量、状态码、配置常量等，支持 TypeScript 类型导出，提供统一的状态码映射和业务常量管理。
metadata:
  type: skill
  scope: scaffold
  triggers:
    - "生成枚举常量"
    - "加一个状态码映射"
    - "写一个业务常量"
    - "封装 xx 的字典"
    - "做个选项配置"
---

# generate-constants

## 何时触发

用户说"生成枚举常量"、"加一个状态码映射"、"写一个业务常量"、"封装 xx 的字典"、"做个选项配置"。

> 业务常量包括：枚举类型、状态码映射、配置常量、字典数据等。统一管理避免硬编码。

## 项目常量结构速查

- **枚举目录**：`constants/` 或 `utils/constants/`
- **命名规范**：`*.constant.ts` 或 `*.enum.ts`
- **TypeScript 支持**：必须提供完整类型定义

常见常量类型：
- 状态枚举：如 `OrderStatus`、`CustomerLevel`
- 映射配置：如 `StatusMap`、`LevelOptions`
- 业务配置：如 `PageSize`、`MaxUploadSize`
- 正则表达式：如 `PhoneRegex`、`EmailRegex`

## 输入要求

| 项 | 说明 | 示例 |
|---|---|---|
| 常量名 | PascalCase，如 `OrderStatus` | `OrderStatus` |
| 常量类型 | enum / map / config / regex | enum |
| 常量内容 | 键值对列表 | `{ 1: '待支付', 2: '已支付' }` |
| 是否需要 TypeScript 类型 | 是 / 否 | 是 |
| 存放位置 | constants/ 或 utils/ | constants/ |

## 常量模板

### 1. 枚举型（enum）

```ts
/**
 * 订单状态枚举
 */
export enum OrderStatus {
  Pending = 1,    // 待支付
  Paid = 2,       // 已支付
  Shipped = 3,    // 已发货
  Completed = 4, // 已完成
  Cancelled = 5,  // 已取消
  Refunded = 6    // 已退款
}

/**
 * 订单状态映射
 */
export const OrderStatusMap: Record<OrderStatus, string> = {
  [OrderStatus.Pending]: '待支付',
  [OrderStatus.Paid]: '已支付',
  [OrderStatus.Shipped]: '已发货',
  [OrderStatus.Completed]: '已完成',
  [OrderStatus.Cancelled]: '已取消',
  [OrderStatus.Refunded]: '已退款'
};

/**
 * 订单状态选项（用于下拉选择）
 */
export const OrderStatusOptions = Object.entries(OrderStatusMap).map(
  ([value, label]) => ({
    value: Number(value),
    label
  })
);
```

### 2. 映射型（map）

```ts
/**
 * 客户等级映射
 */
export const CustomerLevelMap: Record<number, string> = {
  1: 'A类客户',
  2: 'B类客户',
  3: 'C类客户',
  4: 'D类客户'
};

/**
 * 客户等级颜色
 */
export const CustomerLevelColorMap: Record<number, string> = {
  1: '#f5222d', // 红色
  2: '#fa8c16', // 橙色
  3: '#1890ff', // 蓝色
  4: '#999999'  // 灰色
};

/**
 * 客户等级选项
 */
export const CustomerLevelOptions = Object.entries(CustomerLevelMap).map(
  ([value, label]) => ({
    value: Number(value),
    label,
    color: CustomerLevelColorMap[Number(value)]
  })
);
```

### 3. 配置型（config）

```ts
/**
 * 分页配置
 */
export const PaginationConfig = {
  defaultPageSize: 10,
  pageSizeOptions: [10, 20, 50, 100],
  maxPageSize: 100
} as const;

/**
 * 日期格式配置
 */
export const DateFormatConfig = {
  date: 'YYYY-MM-DD',
  time: 'HH:mm:ss',
  datetime: 'YYYY-MM-DD HH:mm:ss',
  month: 'YYYY-MM',
  year: 'YYYY'
} as const;

/**
 * 上传配置
 */
export const UploadConfig = {
  maxSize: 10 * 1024 * 1024, // 10MB
  allowedTypes: ['image/jpeg', 'image/png', 'image/jpg', 'video/mp4'],
  maxFiles: 9
} as const;
```

### 4. 正则型（regex）

```ts
/**
 * 常用正则表达式
 */
export const RegexPatterns = {
  /** 手机号（中国大陆） */
  phone: /^1[3-9]\d{9}$/,

  /** 邮箱 */
  email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,

  /** 身份证号（中国大陆） */
  idCard: /^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/,

  /** 银行卡号 */
  bankCard: /^[1-9]\d{9,29}$/,

  /** URL */
  url: /^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/,

  /** 金额（最多两位小数） */
  amount: /^\d+(\.\d{1,2})?$/
} as const;

/**
 * 正则校验辅助函数
 */
export const RegexUtils = {
  test(pattern: keyof typeof RegexPatterns, value: string): boolean {
    return RegexPatterns[pattern].test(value);
  },

  isPhone(value: string): boolean {
    return RegexPatterns.phone.test(value);
  },

  isEmail(value: string): boolean {
    return RegexPatterns.email.test(value);
  },

  isAmount(value: string | number): boolean {
    return RegexPatterns.amount.test(String(value));
  }
};
```

### 5. 业务状态码

```ts
/**
 * API 业务状态码
 */
export const BizCode = {
  SUCCESS: 0,           // 成功
  PARAM_ERROR: 400,      // 参数错误
  UNAUTHORIZED: 401,    // 未授权
  FORBIDDEN: 403,       // 禁止访问
  NOT_FOUND: 404,       // 资源不存在
  SERVER_ERROR: 500,    // 服务器错误
  NETWORK_ERROR: -1,   // 网络错误
} as const;

/**
 * 业务状态码消息
 */
export const BizCodeMessage: Record<number, string> = {
  [BizCode.SUCCESS]: '操作成功',
  [BizCode.PARAM_ERROR]: '参数错误，请检查输入',
  [BizCode.UNAUTHORIZED]: '登录已过期，请重新登录',
  [BizCode.FORBIDDEN]: '没有权限访问该资源',
  [BizCode.NOT_FOUND]: '请求的资源不存在',
  [BizCode.SERVER_ERROR]: '服务器繁忙，请稍后重试',
  [BizCode.NETWORK_ERROR]: '网络连接失败，请检查网络',
} as const;

/**
 * 获取状态码对应的消息
 */
export function getBizMessage(code: number): string {
  return BizCodeMessage[code] || '未知错误';
}
```

## 执行步骤

1. **追问缺失输入**：确认常量名、类型、内容
2. **选择模板**：按类型选择上述模板
3. **创建目录**（如需要）：
   ```bash
   mkdir -p constants
   ```
4. **生成文件**：按模板生成
5. **统一导出**（可选）：在 `constants/index.ts` 中导出所有常量

### 统一导出示例

```ts
// constants/index.ts
export * from './order.constant';
export * from './customer.constant';
export * from './regex.constant';
export * from './biz-code.constant';
```

## 输出报告

```
已生成常量文件：

constants/order.constant.ts
├── OrderStatus 枚举
├── OrderStatusMap 映射
└── OrderStatusOptions 选项

使用方式：
```ts
import { OrderStatus, OrderStatusMap, OrderStatusOptions } from '@/constants/order.constant';

// 获取状态文本
const text = OrderStatusMap[OrderStatus.Paid]; // '已支付'

// 下拉选择
const options = OrderStatusOptions;
```

⚠️ 需人工确认：
1. 枚举值是否与后端保持一致
2. 是否需要在 API 层或 store 中同步更新
3. 是否需要添加新的枚举值
```

## 红线

- ❌ 禁止在业务代码中硬编码常量值，必须引用常量文件
- ❌ 禁止使用中文拼音命名，必须用有意义的英文/拼音首字母缩写
- ❌ 禁止在枚举中使用字符串值（如 `Pending = 'pending'`），后端通常返回数字
- ❌ 禁止修改已有枚举的键名（如 `Pending` → `PendingPay`），可能导致前端显示异常
- ❌ 禁止使用 `any` 类型，必须提供完整类型定义
