---
name: apifox-sync
description: 将 Laravel 路由属性生成的 API 文档同步到 Apifox。写完后端 Controller 代码后，使用此技能生成 OpenAPI 规范并推送到 Apifox。
version: 1.0.0
source: apifox-openapi
---

# Apifox API 文档同步

## 概述

当用户写完 Laravel Controller 代码后，分析路由属性生成 OpenAPI 3.0 规范，再通过 Python 脚本推送到 Apifox。

## 用户需要提供的信息

首次使用时向用户确认：

- **APIFOX_ACCESS_TOKEN**：Apifox 系统级访问令牌（在 Apifox → 团队设置 → 开放 API → 新建访问令牌）
- **APIFOX_PROJECT_ID**：Apifox 项目ID（默认 4478210）
- **APIFOX_ENDPOINT_FOLDER_ID**（可选）：接口目录ID
- **APIFOX_SCHEMA_FOLDER_ID**（可选）：模型目录ID

写入项目 `.env`：
```
APIFOX_ACCESS_TOKEN=xxx
APIFOX_PROJECT_ID=4478210
APIFOX_ENDPOINT_FOLDER_ID=0
APIFOX_SCHEMA_FOLDER_ID=0
```

## 执行流程

### 第 1 步：分析 Controller 变更

1. 阅读用户本次修改的 Controller 文件
2. 提取路由属性：`#[Prefix]`、`#[Get]`、`#[Post]`、`#[Put]`、`#[Delete]`、`#[Resource]`
3. 提取关联 Request 类的 `rules()` 参数定义
4. 提取方法注释中的 `## 描述` 作为接口说明

### 路由属性 → OpenAPI 映射

| spatie 属性 | OpenAPI |
|------------|---------|
| `#[Prefix('ent/chat/xxx')]` | 路径前缀 |
| `#[Get('path', '描述')]` | `get` + `summary` |
| `#[Post('path', '描述')]` | `post` + `summary` |
| `#[Put('path', '描述')]` | `put` + `summary` |
| `#[Delete('path', '描述')]` | `delete` + `summary` |
| `#[Resource('/', except:[], names:{})]` | 按 names 展开为 index/store/show/update/destroy |
| `#[Middleware(['auth.admin','ent.auth'])]` | `security: [{bearerAuth: []}]` |

### 第 2 步：生成 OpenAPI 3.0 JSON

按下方模板生成 OpenAPI 规范，写入 `storage/app/openapi.json`：

```json
{
    "openapi": "3.0.3",
    "info": { "title": "陀螺匠 API", "version": "2.0.0" },
    "servers": [{ "url": "http://localhost:20200", "description": "本地开发" }],
    "paths": {
        "/ent/chat/mcp-services": {
            "get": {
                "tags": ["MCP服务"],
                "summary": "MCP服务列表",
                "operationId": "get_mcp_services",
                "parameters": [
                    { "name": "app_id", "in": "query", "required": false, "schema": { "type": "integer" }, "description": "应用ID" }
                ],
                "responses": { "200": { "description": "成功" } },
                "security": [{ "bearerAuth": [] }]
            },
            "post": {
                "tags": ["MCP服务"],
                "summary": "保存MCP服务",
                "operationId": "post_mcp_services",
                "requestBody": {
                    "required": true,
                    "content": {
                        "application/json": {
                            "schema": {
                                "type": "object",
                                "required": ["app_id", "name", "type", "service_url"],
                                "properties": {
                                    "app_id": { "type": "integer", "description": "应用ID" },
                                    "name": { "type": "string", "description": "服务名称" },
                                    "type": { "type": "string", "enum": ["sse"], "description": "仅支持SSE" },
                                    "service_url": { "type": "string", "format": "uri" },
                                    "headers": { "type": "string", "description": "请求头JSON" },
                                    "status": { "type": "integer", "enum": [0, 1] },
                                    "sort": { "type": "integer" }
                                }
                            }
                        }
                    }
                },
                "responses": { "200": { "description": "成功" } },
                "security": [{ "bearerAuth": [] }]
            }
        },
        "/ent/chat/mcp-services/{id}": {
            "put": {
                "tags": ["MCP服务"],
                "summary": "修改MCP服务",
                "operationId": "put_mcp_services",
                "parameters": [
                    { "name": "id", "in": "path", "required": true, "schema": { "type": "string" } }
                ],
                "responses": { "200": { "description": "成功" } },
                "security": [{ "bearerAuth": [] }]
            },
            "delete": {
                "tags": ["MCP服务"],
                "summary": "删除MCP服务",
                "operationId": "delete_mcp_services",
                "parameters": [
                    { "name": "id", "in": "path", "required": true, "schema": { "type": "string" } }
                ],
                "responses": { "200": { "description": "成功" } },
                "security": [{ "bearerAuth": [] }]
            }
        },
        "/ent/chat/mcp-services/test-connection": {
            "post": {
                "tags": ["MCP服务"],
                "summary": "测试MCP连接",
                "operationId": "post_mcp_test_connection",
                "requestBody": {
                    "required": true,
                    "content": {
                        "application/json": {
                            "schema": {
                                "type": "object",
                                "required": ["service_url"],
                                "properties": {
                                    "service_url": { "type": "string", "format": "uri" },
                                    "headers": { "type": "string" }
                                }
                            }
                        }
                    }
                },
                "responses": { "200": { "description": "成功" } },
                "security": [{ "bearerAuth": [] }]
            }
        }
    },
    "components": {
        "securitySchemes": {
            "bearerAuth": {
                "type": "http",
                "scheme": "bearer",
                "bearerFormat": "JWT"
            }
        }
    }
}
```

### 第 3 步：运行推送脚本

```bash
python3 .claude/skills/apifox_push.py
```

脚本会自动：
1. 从 `.env` 读取配置
2. 将 OpenAPI 规范字符串直接传给 Apifox（无需公网 URL）
3. 输出导入统计
4. **推送成功后自动清理** `storage/app/openapi.json` 和 `public/api-docs/` 下的残留文件

可选参数：
- `--dry-run` — 验证不推送
- `--mode url` — 改用 URL 方式让 Apifox 从公网拉取（需 `APP_URL` 可公网访问）
- `--endpoint-folder-id` / `--schema-folder-id` — 指定目标目录

### 第 4 步：报告结果

将 Python 脚本的输出展示给用户，包括新增/更新/删除的接口和模型数量。

## 使用方式

当用户说以下内容时触发此技能：

- "把这个接口同步到 Apifox"
- "更新 Apifox 文档"
- "把路由同步到文档"
- "/apifox-sync"
