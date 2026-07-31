---
name: oa-route-attributes
description: 在陀螺匠 OA 项目中新增、调整、排查 Laravel Route Attributes 路由时使用。覆盖 Prefix、Resource、Get/Post/Put/Delete、Middleware、route:list 和 Apifox 同步前检查。
---

# OA 路由属性

## 适用场景

用户要新增接口、修改路径、调整 HTTP 方法、资源路由、排查 404、检查路由中间件或准备同步接口文档时使用。

## 路由写法

```php
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Middleware;

#[Prefix('ent/example')]
#[Middleware(['auth.admin', 'ent.auth', 'ent.log'])]
class ExampleController extends AuthController
{
    #[Get('list', '列表')]
    public function list()
    {
        return $this->success($this->service->getList($this->request->getMore([])));
    }

    #[Post('save', '保存')]
    public function save(ExampleRequest $request)
    {
        return $this->success($this->service->save($request->postMore([])));
    }
}
```

## 项目路径约定

- 后台接口前缀常用 `ent/...`，文件位于 `app/Http/Controller/AdminApi`。
- 移动端接口前缀常用 `uni/...`，文件位于 `app/Http/Controller/UniApi`。
- MCP 或开放能力先查看 `routes/mcp.php` 和 `app/Http/Controller/Mcp`。

## 中间件选择

- 登录态：`auth.admin`
- 企业上下文：`ent.auth`
- 操作日志：`ent.log`
- 模块开关：`module.switch`
- CRUD 动态能力：`ent.crud`

始终先搜索同目录相似 Controller，照已有中间件组合。

## Resource 路由

资源控制器优先参考现有：

```php
#[Resource('/', false, except: ['create', 'show', 'edit'], names: [
    'index' => '列表',
    'store' => '保存',
    'update' => '修改',
    'destroy' => '删除',
])]
```

使用 Resource 时确认：

- 类实现了项目现有资源接口。
- 缺省方法是否都存在。
- `except` 不要遗漏前端仍会调用的方法。

## 排查清单

1. `php artisan route:list | rg '<path-or-controller>'`
2. 检查 Controller 是否在 Route Attributes 扫描目录内。
3. 检查路径是否多写或少写 `/`。
4. 检查 `Prefix` 与方法属性组合后的最终路径。
5. 检查 HTTP 方法是否与前端请求一致。
6. 修改路由后执行 `php artisan route:clear`。

## 文档同步前

- 每个属性第二参数尽量写中文接口说明。
- Request 中规则和消息完整，便于生成 OpenAPI。
- 路径参数在方法签名中显式声明，如 `public function update($id, Request $request)`。
