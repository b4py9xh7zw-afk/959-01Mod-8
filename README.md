# License Platform

一个现代化的许可证管理平台，支持用户管理、许可证创建、查看和管理功能。

## 🛠 技术栈

- **Frontend**: PHP (原生视图) + Tailwind CSS
- **Backend**: PHP 8.2 + PDO
- **Database**: MySQL 8.0 (支持多种数据库类型)
- **Web Server**: Nginx + PHP-FPM
- **Container**: Docker + Docker Compose

## 🚀 启动指南 (How to Run)

### 前置要求

1. 确保已安装 Docker Desktop 并已启动
2. 确保端口 959 和 3306 未被占用

### 启动步骤

1. 在项目根目录执行：
   ```bash
   docker compose up --build
   ```

2. 等待容器启动完成（首次启动可能需要几分钟下载镜像和构建）

3. 访问应用：
   - **Frontend**: http://localhost:959
   - **Database**: localhost:3306 (user: root / pass: root)

## 🧪 测试账号

系统已预置以下测试账号：

- **管理员账号**:
  - 用户名: `admin`
  - 邮箱: `admin@license-platform.com`
  - 密码: `admin123`

- **普通用户账号**:
  - 用户名: `testuser`
  - 邮箱: `user@license-platform.com`
  - 密码: `user123`

## 📋 功能特性

### 用户功能
- ✅ 用户注册和登录
- ✅ 密码加密存储（bcrypt）
- ✅ 基于角色的访问控制（Admin/User）

### 许可证管理
- ✅ 创建许可证（自动生成唯一密钥）
- ✅ 查看许可证详情
- ✅ 许可证状态管理（active/inactive/expired）
- ✅ 许可证过期时间设置
- ✅ 许可证分配（管理员可分配给其他用户）

### 仪表板
- ✅ 统计信息展示（总许可证数、活跃数、过期数等）
- ✅ 最近许可证列表
- ✅ 分页浏览所有许可证
- ✅ 用户管理（仅管理员）

### 安全特性
- ✅ SQL 注入防护（PDO 预处理语句）
- ✅ XSS 防护（输出转义）
- ✅ 密码哈希存储
- ✅ 会话管理
- ✅ 访问控制

## 🏗 项目结构

```
license-platform/
├── app/
│   ├── config/          # 配置文件
│   ├── controllers/     # 控制器
│   ├── models/          # 数据模型
│   ├── views/           # 视图模板
│   └── scripts/         # 数据库初始化脚本
├── public/              # 公共资源
│   ├── css/            # 样式文件
│   ├── js/             # JavaScript 文件
│   └── index.php       # 入口文件
├── uploads/             # 上传文件目录
├── docker-compose.yml   # Docker 编排配置
├── Dockerfile          # PHP 容器配置
└── nginx.conf          # Nginx 配置
```

## 🐳 Docker 服务说明

### 服务组成

1. **db** (MySQL 8.0)
   - 数据库服务
   - 数据持久化到 Docker Volume
   - 自动执行初始化脚本

2. **php** (PHP 8.2-FPM)
   - PHP 应用服务
   - 已安装必要扩展（PDO, MySQL, Zip, MBString）
   - 自动安装 Composer 依赖

3. **nginx** (Nginx Alpine)
   - Web 服务器
   - 反向代理到 PHP-FPM
   - 静态资源服务

### 数据持久化

数据库数据存储在 Docker Volume `db_data` 中，即使容器删除，数据也会保留。

## 🔧 配置说明

### 数据库配置

数据库配置通过环境变量设置（在 `docker-compose.yml` 中）：

- `DB_TYPE`: 数据库类型（mysql/pgsql/sqlite）
- `DB_HOST`: 数据库主机（容器内使用服务名 `db`）
- `DB_PORT`: 数据库端口
- `DB_NAME`: 数据库名称
- `DB_USER`: 数据库用户名
- `DB_PASS`: 数据库密码

### 端口配置

- **前端访问**: `959` (根据项目ID设置)
- **数据库**: `3306`
- **PHP-FPM 内部**: `9000`

## 📝 开发说明

### 数据库初始化

数据库会在容器首次启动时自动初始化，包括：
- 创建表结构
- 插入测试用户
- 插入示例许可证

如需手动初始化，可执行：
```bash
docker compose exec php php app/scripts/init_db.php
```

### 日志查看

查看应用日志：
```bash
docker compose logs -f php
```

查看数据库日志：
```bash
docker compose logs -f db
```

### 停止服务

```bash
docker compose down
```

停止并删除数据卷（**注意：会删除所有数据**）：
```bash
docker compose down -v
```

## 🎨 UI/UX 特性

- ✅ 现代化渐变背景设计
- ✅ 响应式布局（支持移动端和桌面端）
- ✅ 流畅的交互动画和过渡效果
- ✅ 清晰的视觉层次和卡片设计
- ✅ 友好的错误提示和成功反馈
- ✅ 加载状态和骨架屏支持

## 🔒 安全最佳实践

1. **密码安全**: 使用 bcrypt 哈希算法
2. **SQL 注入防护**: 使用 PDO 预处理语句
3. **XSS 防护**: 所有输出使用 `htmlspecialchars()` 转义
4. **会话安全**: 使用 PHP 原生会话管理
5. **访问控制**: 基于角色的权限检查

## 📄 License

本项目为演示项目，仅供学习和参考使用。

---

**注意**: 生产环境部署前，请务必：
- 修改默认密码
- 配置 HTTPS
- 加强安全措施
- 定期备份数据库
