#!/usr/bin/env python3
"""
将 OpenAPI 规范推送到 Apifox.

用法: python3 scripts/apifox_push.py [选项]

从项目 .env 读取配置:
    APIFOX_ACCESS_TOKEN  - Apifox 系统级访问令牌（必填）
    APIFOX_PROJECT_ID    - Apifox 项目ID（必填）
    APIFOX_ENDPOINT_FOLDER_ID - 接口目录ID（可选）
    APIFOX_SCHEMA_FOLDER_ID   - 模型目录ID（可选）
    APP_URL              - 项目公网访问地址（必填，用于 Apifox 拉取 openapi.json）

也可通过命令行参数传入:
    --token       Apifox 访问令牌
    --project-id  Apifox 项目ID
    --base-url    项目公网地址（Apifox 通过此地址拉取 openapi.json）
    --spec-file   OpenAPI 规范文件路径（默认: storage/app/openapi.json）
    --endpoint-folder-id  接口目录ID
    --schema-folder-id    模型目录ID
    --dry-run     仅验证不推送
"""

import argparse
import json
import os
import sys
import urllib.request
import urllib.error


def load_env(env_path):
    """从 .env 文件读取键值对."""
    config = {}
    try:
        with open(env_path) as f:
            for line in f:
                line = line.strip()
                if not line or line.startswith("#") or "=" not in line:
                    continue
                k, v = line.split("=", 1)
                config[k.strip()] = v.strip().strip("\"").strip("'")
    except FileNotFoundError:
        pass
    return config


def push_to_apifox(spec_file, base_url, access_token, project_id, options=None, mode="inline"):
    """推送 OpenAPI 规范到 Apifox 并返回结果.

    mode: 'inline' - 直接传 JSON 数据; 'url' - 让 Apifox 从公网 URL 拉取
    """
    if options is None:
        options = {
            "endpointOverwriteBehavior": "OVERWRITE_EXISTING",
            "schemaOverwriteBehavior": "KEEP_EXISTING",
            "updateFolderOfChangedEndpoint": False,
            "prependBasePath": False,
        }

    # 读取规范文件内容
    with open(spec_file) as f:
        spec_content = json.load(f)

    if mode == "url":
        spec_url = f"{base_url.rstrip('/')}/api-docs/openapi.json"
        input_data = {"url": spec_url}
    else:
        # 直接传 OpenAPI 规范字符串（支持 JSON/YAML/X-YAML）
        input_data = json.dumps(spec_content, ensure_ascii=False)

    body = json.dumps({
        "input": input_data,
        "options": options,
    }, ensure_ascii=False).encode("utf-8")

    url = f"https://api.apifox.com/v1/projects/{project_id}/import-openapi?locale=zh-CN"
    req = urllib.request.Request(
        url,
        data=body,
        headers={
            "X-Apifox-Api-Version": "2024-03-28",
            "Authorization": f"Bearer {access_token}",
            "Content-Type": "application/json",
        },
        method="POST",
    )

    try:
        with urllib.request.urlopen(req, timeout=60) as resp:
            return resp.status, json.loads(resp.read().decode("utf-8"))
    except urllib.error.HTTPError as e:
        return e.code, {"error": True, "message": e.read().decode("utf-8")}
    except urllib.error.URLError as e:
        return 0, {"error": True, "message": str(e.reason)}




def main():
    parser = argparse.ArgumentParser(description="推送 OpenAPI 规范到 Apifox")
    parser.add_argument("--token", help="Apifox 访问令牌")
    parser.add_argument("--project-id", help="Apifox 项目ID")
    parser.add_argument("--base-url", help="项目公网地址")
    parser.add_argument("--spec-file", default="storage/app/openapi.json", help="OpenAPI 规范文件路径")
    parser.add_argument("--endpoint-folder-id", type=int, default=0, help="接口目录ID")
    parser.add_argument("--schema-folder-id", type=int, default=0, help="模型目录ID")
    parser.add_argument("--dry-run", action="store_true", help="仅验证不推送")
    parser.add_argument("--mode", default="inline", choices=["inline", "url"], help="导入方式：inline=直接传数据, url=Apifox从公网拉取")
    parser.add_argument("--env", default=".env", help=".env 文件路径")
    args = parser.parse_args()

    # 读取 .env
    env = load_env(args.env)

    access_token = args.token or env.get("APIFOX_ACCESS_TOKEN") or os.environ.get("APIFOX_ACCESS_TOKEN", "")
    project_id   = args.project_id or env.get("APIFOX_PROJECT_ID") or os.environ.get("APIFOX_PROJECT_ID", "")
    base_url     = args.base_url or env.get("APP_URL") or os.environ.get("APP_URL", "")
    folder_id    = args.endpoint_folder_id or int(env.get("APIFOX_ENDPOINT_FOLDER_ID", 0))
    schema_id    = args.schema_folder_id or int(env.get("APIFOX_SCHEMA_FOLDER_ID", 0))

    # 校验必填项
    errors = []
    if not access_token:
        errors.append("APIFOX_ACCESS_TOKEN 未设置（.env 或 --token）")
    if not project_id:
        errors.append("APIFOX_PROJECT_ID 未设置（.env 或 --project-id）")
    if args.mode == "url" and not base_url:
        errors.append("url 模式需要 APP_URL（.env 或 --base-url）")

    if errors:
        print("❌ 缺少必要配置:")
        for e in errors:
            print(f"   - {e}")
        sys.exit(1)

    # 检查规范文件
    if not os.path.exists(args.spec_file):
        print(f"❌ 规范文件不存在: {args.spec_file}")
        print("   请先生成 OpenAPI 规范 JSON")
        sys.exit(1)

    with open(args.spec_file) as f:
        spec = json.load(f)

    path_count = len(spec.get("paths", {}))
    print(f"📄 规范文件: {args.spec_file} ({path_count} 个接口路径)")
    print(f"📋 导入方式: {'直接传数据' if args.mode == 'inline' else 'URL拉取'}")
    print()

    if args.dry_run:
        print("🔍 --dry-run 模式，不推送")
        return

    # 构造选项
    options = {
        "endpointOverwriteBehavior": "OVERWRITE_EXISTING",
        "schemaOverwriteBehavior": "KEEP_EXISTING",
        "updateFolderOfChangedEndpoint": False,
        "prependBasePath": False,
    }
    if folder_id > 0:
        options["targetEndpointFolderId"] = folder_id
    if schema_id > 0:
        options["targetSchemaFolderId"] = schema_id

    print(f"🚀 推送中...")
    print(f"   项目ID: {project_id}")
    print()

    status, result = push_to_apifox(args.spec_file, base_url, access_token, project_id, options, mode=args.mode)

    if status == 200 and not result.get("error"):
        data    = result.get("data", {})
        counters = data.get("counters", {})

        print("✅ 推送成功!")
        print(f"   接口: 新增 {counters.get('endpointCreated', 0)}, "
              f"更新 {counters.get('endpointUpdated', 0)}, "
              f"失败 {counters.get('endpointFailed', 0)}, "
              f"忽略 {counters.get('endpointIgnored', 0)}")
        print(f"   模型: 新增 {counters.get('schemaCreated', 0)}, "
              f"更新 {counters.get('schemaUpdated', 0)}, "
              f"失败 {counters.get('schemaFailed', 0)}")

        # 清理临时文件
        for f in [args.spec_file, "public/api-docs/openapi.json"]:
            if os.path.exists(f):
                os.remove(f)
                print(f"🧹 已清理: {f}")

        # 清理空目录
        for d in ["public/api-docs", "storage/app"]:
            try:
                os.rmdir(d)
            except OSError:
                pass
    else:
        msg = result.get("message", str(result))
        print(f"❌ 推送失败 (HTTP {status}): {msg}")
        sys.exit(1)


if __name__ == "__main__":
    main()
