---
name: tuoluojiang-oa-patterns
description: 陀螺匠OA系统开发模式和规范，包含分层架构、路由属性、权限系统等核心模式
version: 1.0.0
source: local-analysis
---

# 陀螺匠OA开发模式

## 技术栈

- **PHP**: 8.1+
- **框架**: Laravel 10.0
- **运行时**: Swoole (hhxsv5/laravel-s)
- **认证**: JWT (tymon/jwt-auth)
- **权限**: Casbin (RBAC)
- **路由**: spatie/laravel-route-attributes

## 分层架构（严格遵循）

```
Controller → Service → Dao → Model → Database
```

| 层级 | 职责 | 文件位置 |
|------|------|----------|
| Controller | 接收请求、返回响应 | `app/Http/Controller/` |
| Service | 业务逻辑编排、事务控制 | `app/Http/Service/` |
| Dao | 数据查询封装 | `app/Http/Dao/` |
| Model | Eloquent模型、关系定义 | `app/Http/Model/` |
| Request | 表单验证 | `app/Http/Requests/` |

## 路由定义（路由属性）

使用 PHP 8 属性在控制器中定义路由：

```php
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Put;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Middleware;

#[Prefix('api/admin/user')]
#[Middleware('auth:api')]
class UserController extends BaseController
{
    #[Get('/list')]
    public function list(UserRequest $request)
    {
        return $this->success($this->service->getList($request->validated()));
    }

    #[Post('/create')]
    public function create(UserRequest $request)
    {
        return $this->success($this->service->create($request->validated()));
    }

    #[Put('/{id}/update')]
    public function update(int $id, UserRequest $request)
    {
        return $this->success($this->service->update($id, $request->validated()));
    }

    #[Delete('/{id}/delete')]
    public function delete(int $id)
    {
        $this->service->delete($id);
        return $this->success([], '删除成功');
    }
}
```

## 统一响应格式

```php
// 成功响应
return $this->success($data);                    // 返回数据
return $this->success('操作成功');               // 仅消息
return $this->success($data, [], [], 1);         // 完整参数

// 失败响应
return $this->fail('错误信息');                  // 返回错误
return $this->fail('错误信息', ['field' => 'value']); // 带数据
```

**响应结构：**
```json
{
    "status": 200,
    "message": "ok",
    "data": {},
    "tips": 1
}
```

## 数据权限

### 数据范围常量
- `0` - 全部数据
- `1` - 仅本人数据
- `2` - 本部门数据
- `3` - 指定部门数据
- `4` - 直属下级数据

### 获取数据权限
```php
// 在 Service 中获取用户可访问的用户ID列表
$allowedUids = app(RolesService::class)->getDataUids($userId, $module, $dataRange);

// 在查询中应用权限
$query->whereIn('user_id', $allowedUids);
```

## 新功能开发流程

1. **定义路由** - 在 Controller 中使用路由属性
2. **创建验证** - 创建 Request 类进行参数验证
3. **实现业务** - 在 Service 层实现业务逻辑
4. **数据访问** - 使用 Dao 层封装查询
5. **处理权限** - 在 Service/Dao 层处理数据权限

## 常用命令

```bash
# Swoole 服务
php bin/laravels start       # 启动服务
php bin/laravels reload      # 热重载
./bin/fswatch                # 文件监控自动重载

# Laravel
php artisan route:list       # 查看路由
php artisan migrate          # 数据库迁移
```

## 安全要点

- 所有 API 必须通过 JWT 认证
- 敏感操作需要二次确认
- 禁止在 URL 中传递 token
- 导出数据必须脱敏处理

## 数据库规范

- 禁止 `SELECT *`，必须指定字段
- JOIN 不超过3张表
- 单次查询不超过1000条
- 缓存 key 格式：`模块:资源:标识`
