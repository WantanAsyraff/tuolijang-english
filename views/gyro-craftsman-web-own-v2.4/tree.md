# 项目目录结构说明

## 项目概述
这是一个企业级OA管理系统，基于Vue.js构建，集成了AI应用、低代码平台、客户管理和人事管理等功能模块。

## 目录结构

```
.
├── build/                    # 构建配置相关文件
├── node_modules/             # 项目依赖包
├── patches/                  # 补丁文件
├── public/                   # 静态资源文件
├── src/                      # 源代码主目录
├── static/                   # 静态资源目录
├── tests/                    # 测试文件
├── .editorconfig             # 编辑器配置文件
├── .env.production           # 生产环境配置
├── .eslintignore             # ESLint忽略配置
├── .eslintrc.js              # ESLint代码规范配置
├── .gitignore                # Git忽略配置
├── .node-version             # Node.js版本配置
├── .prettierignore           # Prettier忽略配置
├── .prettierrc.js            # Prettier代码格式化配置
├── Dockerfile                # Docker部署配置
├── README.md                 # 项目说明文档
├── babel.config.js           # Babel编译配置
├── docker-compose.yml        # Docker Compose配置
├── index.js                  # 主入口文件
├── jest.config.js            # Jest测试配置
├── jsconfig.json             # JavaScript配置
├── package-lock.json         # 依赖锁定文件
├── package.json              # 项目配置文件
├── postcss.config.js         # PostCSS配置
└── vue.config.js             # Vue CLI配置文件
```

## 主要功能模块说明

- **AI应用**: 集成人工智能相关功能
- **低代码平台**: 提供快速应用开发能力
- **客户管理**: 客户信息管理和业务流程
- **人事管理**: 员工信息和组织架构管理
- **系统设置**: 系统配置和权限管理

## 技术栈

- Vue.js: 前端框架
- Element UI: UI组件库
- Axios: HTTP客户端
- Vuex: 状态管理
- Vue Router: 路由管理