#!/bin/bash

# 陀螺匠OA系统Docker环境管理脚本
# 支持完整的Docker环境管理功能

set -e

if command -v docker-compose >/dev/null 2>&1; then
    COMPOSE=(docker-compose --env-file .docker.env)
else
    COMPOSE=(docker compose --env-file .docker.env)
fi

detect_php_platform() {
    local host_os
    local host_arch
    host_os=$(uname -s)
    host_arch=$(uname -m)

    if [ -n "${PHP_PLATFORM:-}" ] && [ "$PHP_PLATFORM" != "linux/amd64" ]; then
        echo -e "${YELLOW}提示: 当前 Docker 构建固定适配 docker/php/extensions/swoole_loader80.so，将忽略 PHP_PLATFORM=${PHP_PLATFORM}。${NC}"
    fi

    PHP_PLATFORM=linux/amd64
    PHP_PLATFORM_REASON="固定适配 docker/php/extensions/swoole_loader80.so"

    export PHP_PLATFORM
    unset DOCKER_DEFAULT_PLATFORM
    echo -e "${BLUE}Docker PHP平台: ${PHP_PLATFORM} (host: ${host_os}/${host_arch})${NC}"
    echo -e "${BLUE}平台选择原因: ${PHP_PLATFORM_REASON}${NC}"
}

show_platform() {
    echo -e "${BLUE}PHP容器平台判断:${NC}"
    echo "  宿主系统: $(uname -s)"
    echo "  宿主架构: $(uname -m)"
    echo "  PHP平台: ${PHP_PLATFORM}"
    echo "  Docker默认平台: ${DOCKER_DEFAULT_PLATFORM:-未设置，避免影响MySQL/Redis等非PHP镜像}"
    echo "  判断原因: ${PHP_PLATFORM_REASON:-固定适配 docker/php/extensions/swoole_loader80.so}"
    echo ""
    echo -e "${BLUE}swoole_loader文件格式:${NC}"
    find ./php/extensions -type f -name 'swoole_loader*.so' -exec file {} \; 2>/dev/null || true
}

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# 显示帮助信息
show_help() {
    echo -e "${BLUE}🐳 陀螺匠OA系统Docker环境管理工具${NC}"
    echo ""
    echo "	usage: $0 {start|stop|restart|status|logs|clean|backup|restore|build|platform|exec|help}"
    echo ""
    echo "基础命令:"
    echo "  start     启动所有服务"
    echo "  stop      停止所有服务"
    echo "  restart   重启所有服务"
    echo "  status    查看服务状态"
    echo "  logs      查看实时日志"
    echo ""
    echo "构建与维护:"
    echo "  build     重新构建所有服务镜像"
    echo "  platform  查看PHP容器平台判断结果"
    echo "  clean     清理停止的容器和未使用的镜像"
    echo ""
    echo "数据管理:"
    echo "  backup    备份数据库"
    echo "  restore   恢复数据库"
    echo ""
    echo "开发工具:"
    echo "  exec      进入指定容器"
    echo "  artisan   执行Laravel Artisan命令"
    echo "  composer  执行Composer命令"
    echo ""
    echo "示例:"
    echo "  $0 start                    # 启动所有服务"
    echo "  $0 exec php                 # 进入PHP容器"
    echo "  $0 artisan migrate          # 执行数据库迁移"
    echo "  $0 composer install         # 安装Composer依赖"
    echo ""
}

# 检查是否在正确的目录
check_directory() {
    if [ ! -f "docker-compose.yml" ]; then
        echo -e "${RED}❌ 错误: 请在 docker 目录下执行此脚本${NC}"
        exit 1
    fi

    detect_php_platform
}

# 启动服务
start_services() {
    echo -e "${BLUE}🚀 启动陀螺匠OA系统Docker环境...${NC}"
    "${COMPOSE[@]}" up -d --build
    echo -e "${GREEN}✅ 服务启动完成！${NC}"
    echo -e "${YELLOW}应用查看地址: http://localhost:20300${NC}"
    echo -e "${YELLOW}MySQL端口: 3376 (root/123456)${NC}"
    echo -e "${YELLOW}Redis端口: 6389${NC}"
}

# 停止服务
stop_services() {
    echo -e "${BLUE}🛑 停止所有服务...${NC}"
    "${COMPOSE[@]}" down
    echo -e "${GREEN}✅ 服务已停止${NC}"
}

# 重启服务
restart_services() {
    echo -e "${BLUE}🔄 重启所有服务...${NC}"
    "${COMPOSE[@]}" down
    "${COMPOSE[@]}" up -d --build
    echo -e "${GREEN}✅ 服务重启完成${NC}"
}

# 查看状态
show_status() {
    echo -e "${BLUE}📋 服务状态:${NC}"
    "${COMPOSE[@]}" ps
    echo ""
    echo -e "${BLUE}📊 资源使用情况:${NC}"
    "${COMPOSE[@]}" ps -q | xargs docker stats --no-stream
}

# 查看日志
show_logs() {
    if [ $# -eq 0 ]; then
        echo -e "${BLUE}📝 实时日志 (按 Ctrl+C 退出):${NC}"
        "${COMPOSE[@]}" logs -f
    else
        echo -e "${BLUE}📝 $1 服务日志:${NC}"
        "${COMPOSE[@]}" logs -f "$1"
    fi
}

# 重新构建镜像
build_services() {
    echo -e "${BLUE}🏗️ 重新构建服务镜像...${NC}"
    "${COMPOSE[@]}" build --no-cache
    echo -e "${GREEN}✅ 镜像构建完成${NC}"
}

# 清理资源
clean_resources() {
    echo -e "${BLUE}🧹 清理当前项目Docker资源...${NC}"
    "${COMPOSE[@]}" down --remove-orphans
    docker image prune -f
    docker builder prune -f
    echo -e "${GREEN}✅ 清理完成${NC}"
}

# 备份数据库
backup_database() {
    echo -e "${BLUE}💾 开始备份数据库...${NC}"
    timestamp=$(date +"%Y%m%d_%H%M%S")
    backup_file="backup_${timestamp}.sql"
    
    "${COMPOSE[@]}" exec mysql mysqldump -u root -p123456 tuoluojiang > "$backup_file"
    echo -e "${GREEN}✅ 数据库备份完成: $backup_file${NC}"
}

# 恢复数据库
restore_database() {
    if [ $# -eq 0 ]; then
        echo -e "${RED}❌ 请指定备份文件${NC}"
        echo "用法: $0 restore <backup_file.sql>"
        exit 1
    fi
    
    backup_file=$1
    if [ ! -f "$backup_file" ]; then
        echo -e "${RED}❌ 备份文件不存在: $backup_file${NC}"
        exit 1
    fi
    
    echo -e "${BLUE}🔄 恢复数据库: $backup_file${NC}"
    "${COMPOSE[@]}" exec -T mysql mysql -u root -p123456 tuoluojiang < "$backup_file"
    echo -e "${GREEN}✅ 数据库恢复完成${NC}"
}

# 进入容器
enter_container() {
    if [ $# -eq 0 ]; then
        echo -e "${RED}❌ 请指定容器名称${NC}"
        echo "可用容器: php, mysql, redis"
        exit 1
    fi
    
    case "$1" in
        php)
            echo -e "${BLUE}进入PHP容器...${NC}"
            "${COMPOSE[@]}" exec php bash
            ;;
        mysql)
            echo -e "${BLUE}进入MySQL容器...${NC}"
            "${COMPOSE[@]}" exec mysql mysql -u root -p123456
            ;;
        redis)
            echo -e "${BLUE}进入Redis容器...${NC}"
            "${COMPOSE[@]}" exec redis redis-cli
            ;;
        *)
            echo -e "${RED}❌ 未知容器: $1${NC}"
            echo "可用容器: php, mysql, redis"
            exit 1
            ;;
    esac
}

# 执行Artisan命令
run_artisan() {
    if [ $# -eq 0 ]; then
        echo -e "${BLUE}Laravel Artisan命令:${NC}"
        "${COMPOSE[@]}" exec php php artisan
    else
        echo -e "${BLUE}执行Artisan命令: $*${NC}"
        "${COMPOSE[@]}" exec php php artisan "$@"
    fi
}

# 执行Composer命令
run_composer() {
    if [ $# -eq 0 ]; then
        echo -e "${BLUE}Composer命令:${NC}"
        "${COMPOSE[@]}" exec php composer
    else
        echo -e "${BLUE}执行Composer命令: $*${NC}"
        "${COMPOSE[@]}" exec php composer "$@"
    fi
}

# 主程序
main() {
    check_directory
    
    case "$1" in
        start)
            start_services
            ;;
        stop)
            stop_services
            ;;
        restart)
            restart_services
            ;;
        status)
            show_status
            ;;
        logs)
            shift
            show_logs "$@"
            ;;
        build)
            build_services
            ;;
        platform)
            show_platform
            ;;
        clean)
            clean_resources
            ;;
        backup)
            backup_database
            ;;
        restore)
            shift
            restore_database "$@"
            ;;
        exec)
            shift
            enter_container "$@"
            ;;
        artisan)
            shift
            run_artisan "$@"
            ;;
        composer)
            shift
            run_composer "$@"
            ;;
        help|"")
            show_help
            ;;
        *)
            echo -e "${RED}❌ 未知命令: $1${NC}"
            show_help
            exit 1
            ;;
    esac
}

# 执行主程序
main "$@"
