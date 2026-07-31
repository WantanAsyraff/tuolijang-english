---
name: oa-feature-scaffold
description: 在陀螺匠 OA Laravel 10 + Swoole 项目中新增业务功能、模块、CRUD 接口或资源控制器时使用。帮助按 Controller、Request、Service、Dao、Model 分层落地二次开发。
---

# OA 功能脚手架

## 适用场景

用户要新增业务模块、CRUD、后台接口、移动端接口、管理功能、流程入口或一组相关 API 时使用本 skill。

## 开发顺序

1. 先搜索同模块相近实现，优先复用现有命名、目录、异常、响应、权限和缓存模式。
2. 按 `Controller -> Request -> Service -> Dao -> Model` 分层实现，不跨层调用。
3. Controller 只接收请求、调用 Service、返回 `success()` 或 `fail()`。
4. Service 负责业务编排、事务、权限、缓存、事件、任务投递。
5. Dao 负责查询封装，Model 负责表名、字段、关联、搜索器。
6. 修改后运行最小验证：语法检查、相关测试、路由列表或目标接口检查。

## 目录选择

- 后台管理接口：`app/Http/Controller/AdminApi/{Module}`
- 移动端接口：`app/Http/Controller/UniApi/{Module}`
- MCP 接口：`app/Http/Controller/Mcp`
- Service：`app/Http/Service/{Module}`
- Dao：`app/Http/Dao/{Module}`
- Model：`app/Http/Model/{Module}`
- Request：`app/Http/Requests/{domain}`
- 常量：`app/Constants`

## 控制器约束

- 使用 PHP 8 路由属性：`#[Prefix]`、`#[Get]`、`#[Post]`、`#[Put]`、`#[Delete]`、`#[Resource]`。
- 管理端优先继承 `AuthController`；纯基础接口再参考相近文件决定是否继承 `BaseController`。
- 资源控制器参考现有 `ResourceControllerInterface` 实现。
- 方法名保持项目习惯：控制器方法可用小写下划线；普通业务方法用 camelCase。
- 不在 Controller 写查询、事务和复杂条件判断。

## Service 约束

- 构造函数注入 Dao，遵循现有 `BaseService` 模式。
- 多表写入、审批、扣减、批量更新必须放事务。
- 写操作后同步清理相关缓存或投递任务。
- 抛业务异常时优先使用项目已有异常类和消息风格。
- 数据权限在 Service 或 Dao 中处理，不放到 Controller。

## Dao/Model 约束

- Dao 的 `search($where, ?bool $authWhere = null)` 只拼查询条件，不处理业务决策。
- 禁止新写 `select *`；查询字段必须明确，除非局部已有模式必须兼容。
- 分页、排序、with 关联参考同模块 Dao。
- Model 中补齐表名、主键、时间字段、fillable/guarded、关联和搜索器。

## 完成检查

- `php -l <changed.php>` 检查新改 PHP 文件。
- `php artisan route:list` 验证路由属性是否被发现。
- 涉及数据库时检查迁移字段、索引、默认值和软删除/时间戳约定。
- 涉及 Swoole 常驻内存时避免静态缓存请求态数据。
