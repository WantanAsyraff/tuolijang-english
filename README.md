# 陀螺匠 · 企业助手

陀螺匠是企业级 OA 管理系统，后端基于 Laravel 9 + LaravelS/Swoole，集成 MySQL、Redis、JWT 认证、Casbin RBAC 权限、WebSocket、企业微信、WPS 在线文档等能力。

## 技术栈

| 类型 | 说明 |
|------|------|
| PHP | PHP 8.0.2+，Docker 环境固定使用 PHP 8.0 |
| 框架 | Laravel 9.52 |
| 常驻服务 | LaravelS 3.7.43 + Swoole 4.8 |
| 数据存储 | MySQL 8.0、Redis 7 |
| 认证授权 | JWT、Casbin RBAC |
| 路由 | spatie/laravel-route-attributes |
| Composer 源 | 腾讯云 Composer 镜像 |

## 运行要求

基础 PHP 扩展：

- `bcmath`
- `curl`
- `fileinfo`
- `gd`
- `json`
- `mbstring`
- `openssl`
- `pcntl`
- `pdo_mysql`
- `redis`
- `sockets`
- `swoole`
- `tokenizer`
- `xml`
- `zip`

项目包含加密文件，运行环境必须安装与系统架构和 PHP 版本匹配的 `swoole_loader`。Docker 环境会自动选择并加载 Linux 版 `swoole_loader`。

## Docker 初始化安装

推荐使用 Docker 完成首次安装。Docker 环境包含 PHP、MySQL、Redis 三个服务；首次启动只创建 `tuoluojiang` 空数据库，不创建业务表。业务表和初始化数据由浏览器安装页面创建。

### 1. 启动服务

```shell
cd docker
docker compose --env-file .docker.env up -d --build
```

也可以使用管理脚本：

```shell
cd docker
./manage.sh start
```

启动过程中 PHP 容器会自动完成：

1. 根据容器 CPU 架构加载 Linux 版 `swoole_loader`。
2. 项目根目录不存在 `.env` 时，复制 `.env.example` 为 `.env`。
3. 将 `.env` 中的 MySQL / Redis 配置写为容器内服务地址。
4. `vendor` 不存在或与 PHP 8.0 不兼容时，重新执行 `composer install`。

容器内连接配置如下：

```env
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=tuoluojiang
DB_USERNAME=root
DB_PASSWORD=123456

REDIS_HOST=redis
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

### 2. 检查服务状态

```shell
cd docker
docker compose --env-file .docker.env ps
```

确认 `php`、`mysql`、`redis` 三个服务均为 `healthy`。

宿主机访问端口：

| 服务 | 宿主机地址 | 容器内地址 |
|------|------------|------------|
| 应用 | `http://127.0.0.1:20300` | `php:20200` |
| MySQL | `127.0.0.1:3376` | `mysql:3306` |
| Redis | `127.0.0.1:6389` | `redis:6379` |

### 3. 确认数据库为空

首次安装前，`tuoluojiang` 数据库应存在，但业务表数量应为 `0`：

```shell
cd docker
docker compose --env-file .docker.env exec -T mysql \
  mysql -uroot -p123456 -e "SELECT COUNT(*) AS tables_count FROM information_schema.TABLES WHERE TABLE_SCHEMA='tuoluojiang';"
```

期望结果：

```text
tables_count
0
```

### 4. 访问安装页面

```text
http://127.0.0.1:20300/install
```

按页面提示完成系统安装。安装流程会创建业务数据表并写入初始化数据。

### 5. 清空 MySQL 重新安装

如果本机已存在旧 Docker 数据卷，MySQL 会复用旧数据。需要重新走安装流程时，先删除并重建 MySQL 数据卷：

```shell
cd docker
docker compose --env-file .docker.env down
docker volume rm tuoluojiang_mysql_data
docker compose --env-file .docker.env up -d --build
```

注意：删除 `tuoluojiang_mysql_data` 会清除 Docker MySQL 中已有数据。生产或已有业务数据环境请先备份。

## 手动安装流程

手动安装适用于已准备好 `.env`、Composer 依赖、应用密钥、PHP、MySQL、Redis、Swoole、`swoole_loader` 的服务器。此处只说明服务启动和页面安装步骤。

1. 可选：创建本地文件软链：

   ```shell
   php artisan storage:link
   ```

2. 启动 LaravelS：

   ```shell
   php bin/laravels start
   ```

   生产环境可使用守护进程模式：

   ```shell
   php bin/laravels start -d
   ```

3. 通过浏览器访问 `/install` 完成系统安装。

## LaravelS 常用命令

```shell
php bin/laravels start
php bin/laravels start -d
php bin/laravels stop
php bin/laravels restart
php bin/laravels reload
php bin/laravels info
```

| 命令 | 说明 |
|------|------|
| `start` | 启动 LaravelS |
| `start -d` | 以守护进程方式启动 LaravelS |
| `stop` | 停止 LaravelS |
| `restart` | 重启 LaravelS |
| `reload` | 平滑重载 Worker / Task / Timer 进程 |
| `info` | 查看组件版本和服务信息 |

常用启动选项：

| 选项 | 说明 |
|------|------|
| `-d, --daemonize` | 守护进程模式 |
| `-e, --env` | 指定运行环境，例如 `--env=testing` |
| `-i, --ignore` | 忽略 Master 进程 PID 文件检查 |
| `-x, --x-version` | 写入当前工程版本号到运行环境 |

## 热更新开发

项目已内置热更新能力。开发环境启动后会自动监听代码变更并 reload LaravelS Worker，无需额外安装 `fswatch`、`inotify` 或执行额外监听命令。

## Docker 常用命令

以下命令均在 `docker` 目录执行：

```shell
cd docker

# 首次启动或 Dockerfile / entrypoint.sh 变更后启动
docker compose --env-file .docker.env up -d --build

# 日常启动
docker compose --env-file .docker.env up -d

# 查看状态
docker compose --env-file .docker.env ps

# 停止
docker compose --env-file .docker.env down

# 重启 PHP 服务
docker compose --env-file .docker.env restart php

# 查看日志
docker compose --env-file .docker.env logs -f
docker compose --env-file .docker.env logs -f php
docker compose --env-file .docker.env logs -f mysql
docker compose --env-file .docker.env logs -f redis

# 进入容器
docker compose --env-file .docker.env exec php bash
docker compose --env-file .docker.env exec mysql mysql -u root -p123456
docker compose --env-file .docker.env exec redis redis-cli
```

更多 Docker 说明见 [docker/README.md](docker/README.md)。

## 开发规范

### 命名规范

| 类型 | 规范 | 示例 |
|------|------|------|
| 类名 | PascalCase | `UserController` |
| 方法名 | camelCase | `getUserInfo` |
| 控制器方法 | 小写加下划线 | `get_client_ip` |
| 普通函数 | 小写加下划线 | `get_client_ip` |
| 属性 / 变量 | camelCase | `$userId` |
| 常量 | SNAKE_CASE | `MAX_UPLOAD_SIZE` |
| 数据表 | 小写加下划线 | `system_user` |
| 字段名 | 小写加下划线 | `user_name` |

### 分层约定

```text
HTTP Request
  -> Middleware
  -> Controller
  -> Service
  -> Dao
  -> Model
  -> Database
```

业务逻辑应放在 Service 层，数据访问应通过 Dao 层封装，避免 Controller 直接操作 Model 或跨层调用。

## 重要目录

```text
app/Http/Controller/   控制器
app/Http/Service/      业务服务
app/Http/Dao/          数据访问
app/Http/Model/        Eloquent 模型
app/Jobs/              队列任务
config/laravels.php    LaravelS / Swoole 配置
routes/install.php     安装路由
routes/mcp.php         MCP 路由
routes/websocket.php   WebSocket 路由
crmeb/                 基础能力、工具类和第三方服务封装
docker/                Docker 运行环境
```

## 帮助文档

- 官方文档：https://doc.crmeb.com/tuoluojiang

