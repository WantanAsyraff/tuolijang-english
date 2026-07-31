# 🐳 陀螺匠OA系统Docker集成环境

这是一个专为陀螺匠OA系统设计的完整Docker集成环境，包含PHP、MySQL、Redis三个核心服务。首次启动只初始化运行环境和空数据库，业务表与初始化数据通过浏览器安装页面创建。

## 📁 目录结构

```
docker/
├── docker-compose.yml          # Docker编排文件
├── docker-compose.ha.yml       # 高可用部署示例配置
├── .docker.env                 # 环境变量配置
├── manage.sh                   # 管理脚本
├── README.md                   # 使用说明
├── php/
│   ├── Dockerfile              # PHP CLI镜像构建文件
│   ├── entrypoint.sh           # PHP容器入口脚本
│   ├── config/
│   │   └── php.ini             # PHP配置文件
│   └── extensions/
│       └── swoole_loader*.so   # Linux版Swoole Loader扩展
├── mysql/
│   ├── config/
│   │   └── my.cnf              # MySQL配置文件
│   ├── data/                   # 历史目录；当前MySQL数据以Docker命名卷为准
│   ├── logs/                   # MySQL日志目录
│   └── init/
│       └── init-db.sh          # 数据库初始化脚本：仅创建数据库和授权用户
└── redis/
    ├── config/
    │   └── redis.conf          # Redis配置文件
    └── data/                   # Redis数据目录
```

## 🚀 核心特性

### PHP环境 (8.0 CLI)
- ✅ 基于PHP 8.0 CLI版本，专为LaravelS优化
- ✅ 集成Swoole扩展，支持高性能异步处理
- ✅ 集成Redis扩展，提供缓存和队列支持
- ✅ 固定PHP 8.0，并根据容器CPU架构和NTS/ZTS自动安装Linux版`swoole_loader80`扩展，用于运行加密文件
- ✅ 预装Composer依赖管理工具，默认使用腾讯云Composer镜像
- ✅ 优化的PHP配置参数

### MySQL数据库 (8.0)
- ✅ UTF8MB4字符集支持，完美支持中文
- ✅ 优化的性能配置参数
- ✅ 自动创建空数据库 `tuoluojiang`
- ✅ 使用Docker命名卷持久化数据
- ✅ 详细的慢查询日志记录

### Redis缓存 (7-alpine)
- ✅ 轻量级Alpine Linux基础镜像
- ✅ 完整的配置文件支持
- ✅ 数据持久化机制
- ✅ 性能优化配置

## 📦 初始化安装流程

### 1. 准备环境

确认本机已安装 Docker Engine / Docker Desktop，并且以下宿主机端口未被占用：

- `20300`：应用访问端口
- `3376`：MySQL 调试端口
- `6389`：Redis 调试端口

项目 Docker 初始化会自动处理：

- 仅当项目根目录 `.env` 不存在时，复制 `.env.example` 为 `.env`
- 仅首次生成 `.env` 时写入容器内 MySQL / Redis 默认地址；后续启动保留现有 `.env` 配置，便于调试外部数据库
- 固定 PHP 8.0，并根据容器 CPU 架构和NTS/ZTS安装 Linux ELF 版 `swoole_loader80`
- 启动前清理 Laravel 可再生缓存，避免旧 provider 缓存影响启动
- 使用 PHP 8.0 重新安装不兼容或缺失的 `vendor`

> Docker 容器内始终是 Linux 环境，Windows / macOS / Linux 宿主机都不能直接加载各自宿主机格式的扩展文件。当前 Docker 部署固定适配 `docker/php/extensions/swoole_loader80.so`，因此 PHP 8.0 容器平台固定为 `linux/amd64`。`swoole_loader80_arm64.so` 如为 Mach-O 则是 macOS 版，不能用于 Docker Linux 容器。

### 2. 启动服务
```bash
cd docker
./manage.sh start
# 或者直接使用docker compose
docker compose --env-file .docker.env up -d
```

### 3. 验证服务状态
```bash
./manage.sh status
# 或者
docker compose --env-file .docker.env ps
```

三个服务均为 `healthy` 后继续安装。

### 4. 确认数据库为空

首次安装时，MySQL 容器只应创建 `tuoluojiang` 数据库，不应创建业务表：

```bash
docker compose --env-file .docker.env exec -T mysql \
  mysql -uroot -p123456 -e "SELECT COUNT(*) AS tables_count FROM information_schema.TABLES WHERE TABLE_SCHEMA='tuoluojiang';"
```

期望结果：

```text
tables_count
0
```

### 5. 访问安装页面

- **安装地址**: http://localhost:20300/install
- **应用地址**: http://localhost:20300
- **MySQL管理**: localhost:3376 (用户: root, 密码: 123456)
- **Redis服务**: localhost:6389

按安装页面提示完成系统安装。安装流程会创建业务数据表并写入初始化数据。

## 🛠️ 管理命令

### 使用管理脚本 (推荐)
```bash
./manage.sh start      # 启动所有服务
./manage.sh stop       # 停止所有服务
./manage.sh restart    # 重启所有服务
./manage.sh status     # 查看服务状态
./manage.sh logs       # 查看实时日志
./manage.sh backup     # 备份数据库
./manage.sh restore    # 恢复数据库
./manage.sh clean      # 清理Docker资源
./manage.sh help       # 显示帮助信息
```

### 直接使用Docker Compose
```bash
# 启动服务
docker compose --env-file .docker.env up -d

# 停止服务
docker compose --env-file .docker.env down

# 查看日志
docker compose --env-file .docker.env logs -f

# 进入容器
docker compose --env-file .docker.env exec php bash
docker compose --env-file .docker.env exec mysql mysql -u root -p123456
docker compose --env-file .docker.env exec redis redis-cli

# 重建服务
docker compose --env-file .docker.env down
docker compose --env-file .docker.env up --build -d
```

## 🔧 环境配置

### 数据库配置
```
主机: mysql (容器内) / localhost (宿主机)
端口: 3306 (容器内) / 3376 (宿主机)
数据库: tuoluojiang
用户名: root
密码: 123456
字符集: utf8mb4
```

### Redis配置
```
主机: redis (容器内) / localhost (宿主机)
端口: 6379 (容器内) / 6389 (宿主机)
密码: 无
数据库: 0-15
```

### PHP配置
```
版本: 8.0 CLI
内存限制: 512M
最大执行时间: 300秒
上传文件大小: 100M
扩展: Swoole, Redis, APCu, swoole_loader, PDO MySQL, GD
```

### Composer配置
```
镜像源: https://mirrors.cloud.tencent.com/composer/
安装命令: composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
```

## 💾 数据持久化

所有重要数据均已配置持久化存储：
- **MySQL数据**: Docker命名卷 `tuoluojiang_mysql_data`
- **MySQL日志**: `./mysql/logs/` 目录
- **Redis数据**: `./redis/data/` 目录
- **应用代码**: 通过volume挂载实现实时同步

### 清空MySQL重新安装

如果本机已经存在旧数据卷，MySQL 会复用旧数据。重新走安装流程前需要删除并重建 MySQL 数据卷：

```bash
cd docker
docker compose --env-file .docker.env down
docker volume rm tuoluojiang_mysql_data
docker compose --env-file .docker.env up -d --build
```

注意：删除 `tuoluojiang_mysql_data` 会清除 Docker MySQL 中的全部数据。已有业务数据时请先备份。

## 🔧 开发工具

### 应用管理
```bash
# 执行Artisan命令
docker compose --env-file .docker.env exec php php artisan migrate
docker compose --env-file .docker.env exec php php artisan db:seed
docker compose --env-file .docker.env exec php php artisan cache:clear

# Composer操作
docker compose --env-file .docker.env exec php composer install
docker compose --env-file .docker.env exec php composer update
```

### 数据库管理
```bash
# 数据库备份
docker compose --env-file .docker.env exec mysql mysqldump -u root -p123456 tuoluojiang > backup.sql

# 数据库恢复
docker compose --env-file .docker.env exec -T mysql mysql -u root -p123456 tuoluojiang < backup.sql

# 进入MySQL命令行
docker compose --env-file .docker.env exec mysql mysql -u root -p123456
```

## ⚠️ 注意事项

### 首次使用
1. **构建时间**: 首次启动需要下载基础镜像和构建，耗时较长
2. **磁盘空间**: 确保有足够的磁盘空间（建议至少5GB）
3. **内存要求**: 建议系统内存至少4GB

### 运行环境
1. **端口占用**: 确保20300、3376、6389端口未被占用
2. **权限设置**: 确保docker目录具有适当的读写权限
3. **Docker版本**: 建议使用Docker Engine 20.10+版本

### 生产部署
1. **安全配置**: 生产环境请修改默认密码
2. **备份策略**: 建立定期备份机制
3. **监控告警**: 配置系统监控和异常告警
4. **资源限制**: 根据实际需求调整容器资源限制

## 🆘 故障排除

### 常见问题

**1. 端口冲突**
```bash
# 检查端口占用
lsof -i :20300
lsof -i :3376
lsof -i :6389

# 修改端口映射在 docker-compose.yml 中
```

**2. 权限问题**
```bash
# 修复目录权限
sudo chown -R $(id -u):$(id -g) .
sudo chmod -R 755 .
```

**3. 构建失败**
```bash
# 清理构建缓存
docker builder prune -f
docker compose --env-file .docker.env build --no-cache
```

**4. 服务无法启动**
```bash
# 查看详细日志
docker compose --env-file .docker.env logs -f --tail=100
docker compose --env-file .docker.env logs php
docker compose --env-file .docker.env logs mysql
docker compose --env-file .docker.env logs redis
```

**5. Composer安装失败**
```bash
# 查看PHP容器日志
docker compose --env-file .docker.env logs php

# 如果日志提示已停止自动重试，处理网络后删除失败标记并重启
rm -f ../storage/framework/docker-composer-install.failed
docker compose --env-file .docker.env restart php
```

**6. 数据库不是空库**
```bash
# 确认表数量
docker compose --env-file .docker.env exec -T mysql \
  mysql -uroot -p123456 -e "SELECT COUNT(*) AS tables_count FROM information_schema.TABLES WHERE TABLE_SCHEMA='tuoluojiang';"

# 需要重新安装时清空数据卷
docker compose --env-file .docker.env down
docker volume rm tuoluojiang_mysql_data
docker compose --env-file .docker.env up -d --build
```

### 日志查看
```bash
# 实时查看所有服务日志
./manage.sh logs

# 查看特定服务日志
docker compose --env-file .docker.env logs -f php
docker compose --env-file .docker.env logs -f mysql
docker compose --env-file .docker.env logs -f redis

# 查看最近的日志
docker compose --env-file .docker.env logs --tail=50
```

## 🧱 高可用配置

当前默认使用 `docker-compose.yml` 作为本地开发和单机初始化配置。已额外保留可独立启动的 `docker-compose.ha.yml`，包含 Nginx 上游转发、两个 PHP/LaravelS 节点、持久化卷和 Redis AOF 策略：

```bash
cd docker
docker compose -f docker-compose.ha.yml up -d --build
```

注意：这份配置保留为高可用部署模板。生产使用时建议放在 Swarm、Kubernetes 或外部 SLB 后面，并将 MySQL/Redis 替换为托管集群或主从哨兵方案。

## 📊 性能优化建议

### PHP优化
- 调整OPcache配置参数
- 根据应用负载调整进程数
- 启用适当的预加载机制

### MySQL优化
- 根据数据量调整缓冲池大小
- 优化慢查询SQL语句
- 定期分析表和索引

### Redis优化
- 根据使用场景选择合适的持久化策略
- 配置适当的内存淘汰策略
- 监控内存使用情况

---
**提示**: 本环境已移除原有的integrated目录结构，采用扁平化设计，更便于日常使用和维护。
