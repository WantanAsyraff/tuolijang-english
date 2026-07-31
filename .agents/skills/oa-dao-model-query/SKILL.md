---
name: oa-dao-model-query
description: 在陀螺匠 OA 项目中编写 Dao、Model、Eloquent 查询、搜索条件、分页、关联加载、索引友好查询和 SQL 优化时使用。
---

# OA Dao 与 Model 查询

## 适用场景

新增 Dao/Model、调整查询条件、处理列表分页、优化 SQL、增加关联、排查慢查询或字段返回时使用。

## Dao 结构

```php
class ExampleDao extends BaseDao
{
    protected function setModel(): string
    {
        return Example::class;
    }

    public function search($where, ?bool $authWhere = null)
    {
        return $this->getModel(false)
            ->when(isset($where['name']) && $where['name'] !== '', function ($query) use ($where) {
                $query->where('name', 'like', '%' . $where['name'] . '%');
            })
            ->when(isset($where['status']) && $where['status'] !== '', function ($query) use ($where) {
                $query->where('status', $where['status']);
            });
    }
}
```

先搜索相近 Dao，保持 `search()`、`getList()`、`select($field)`、`with($with)`、分页方式一致。

## 查询规范

- 禁止新写 `SELECT *`，除非兼容旧方法签名必须传 `['*']`。
- 列表接口明确字段，例如 `['id', 'name', 'status', 'created_at']`。
- JOIN 不超过 3 张表；复杂查询优先拆分或用子查询。
- 单次查询返回不超过 1000 条，列表必须分页或限制数量。
- 大偏移分页要谨慎，优先基于 ID 或时间游标。
- `whereIn` 数组可能很大时先限制长度或分批。

## Model 要点

- 明确表名、主键、时间字段。
- 关联方法命名清晰，返回 `hasOne`、`hasMany`、`belongsTo` 等。
- 搜索器和访问器不要发起复杂查询。
- 避免在 Model 中调用 Service。

## 排序与分页

常见模式：

```php
return $this->search($where)
    ->select($field)
    ->when($page && $limit, fn ($query) => $query->forPage($page, $limit))
    ->orderByDesc('id')
    ->with($with)
    ->get();
```

排序字段来自用户输入时必须白名单化，不能直接拼接任意字段。

## 安全与性能

- 模糊搜索字段需要确认索引和数据量。
- 范围查询字段放在复合索引后部。
- 等值过滤字段放在复合索引前部。
- 原生 SQL 必须使用绑定参数，除非完全由固定枚举拼接。
- 导出查询必须先做权限过滤和字段脱敏。

## 验证方法

- 相关文件 `php -l`。
- 复杂 SQL 可用 `toSql()` 和 bindings 临时检查，完成后移除调试代码。
- 需要时补充索引迁移。
