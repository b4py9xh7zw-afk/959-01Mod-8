# 黑灰名单密钥吊销 API 使用说明

## 功能概述

发现泄露授权码、异常批量激活或盗版镜像后，安全人员可以：
- 把密钥放入**黑名单**：立即停用许可证
- 把密钥放入**灰名单**：许可证仍可使用，但客户端会显示警告
- 误封恢复：记录恢复范围和负责人

---

## 一、客户端验证 API

### 许可证验证端点

**端点**: `GET /api/license/validate` 或 `POST /api/license/validate`

**参数**:
| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| license_key | string | 是 | 许可证密钥 |

**请求示例**:
```bash
curl -X GET "https://your-domain.com/api/license/validate?license_key=ABCD-1234-EFGH-5678"
```

```bash
curl -X POST "https://your-domain.com/api/license/validate" \
  -d "license_key=ABCD-1234-EFGH-5678"
```

### 响应说明

#### 1. 许可证正常（有效）
```json
{
  "valid": true,
  "license": {
    "id": 1,
    "license_key": "ABCD-1234-EFGH-5678",
    "product_name": "专业版软件",
    "status": "active",
    "expires_at": "2027-12-31 23:59:59"
  }
}
```

#### 2. 许可证在黑名单中（已吊销）
```json
{
  "valid": false,
  "message": "License has been revoked",
  "revocation_reason": "发现该授权码在盗版镜像中传播",
  "appeal_channel": "support@example.com 或 400-888-8888",
  "license": {
    "id": 1,
    "license_key": "ABCD-1234-EFGH-5678",
    "product_name": "专业版软件",
    "status": "blacklisted"
  }
}
```

**客户端处理逻辑**:
- 阻止软件继续运行
- 显示吊销原因和申诉渠道
- 提供申诉按钮或联系方式

#### 3. 许可证在灰名单中（观察中）
```json
{
  "valid": true,
  "warning": true,
  "message": "License is under observation",
  "revocation_reason": "检测到异常批量激活行为，正在核实中",
  "appeal_channel": "support@example.com",
  "license": {
    "id": 2,
    "license_key": "WXYZ-9876-ABCD-5432",
    "product_name": "专业版软件",
    "status": "greylisted"
  }
}
```

**客户端处理逻辑**:
- 允许软件继续运行
- 显示警告信息、原因和申诉渠道
- 建议用户联系客服核实

---

## 二、管理控制台操作

### 1. 加入黑名单

**路径**: `/licenses/view?id={license_id}`

**操作**:
1. 登录管理员账号
2. 进入许可证详情页
3. 点击「加入黑名单」按钮
4. 填写吊销原因和申诉渠道
5. 确认操作

### 2. 加入灰名单

**路径**: `/licenses/view?id={license_id}`

**操作**:
1. 登录管理员账号
2. 进入许可证详情页
3. 点击「加入灰名单」按钮
4. 填写观察原因和申诉渠道
5. 确认操作

### 3. 恢复许可证

**路径**: `/licenses/view?id={license_id}`

**操作**:
1. 登录管理员账号
2. 进入许可证详情页
3. 点击「恢复许可证」按钮
4. 填写以下信息：
   - 恢复原因（如：误封、经核实无违规行为）
   - 恢复范围（如：仅本许可证、该用户所有许可证）
   - 负责人（如：张三（安全主管））
5. 确认操作

### 4. 查看吊销日志

**路径**: `/licenses/revocation-logs`

**功能**:
- 查看所有黑名单/灰名单/恢复操作记录
- 按操作类型筛选
- 查看操作人、时间、原因、恢复范围、负责人等审计信息

---

## 三、数据库迁移

### 首次部署

数据库初始化脚本已包含黑灰名单相关表结构：
- `init.sql` 已更新

### 已有数据库升级

运行迁移脚本：
```bash
php app/scripts/migrate_revocation.php
```

该脚本会自动：
1. 修改 `licenses` 表的 `status` 字段枚举值
2. 添加 `revocation_reason` 和 `appeal_channel` 字段
3. 创建 `license_revocation_logs` 审计日志表

---

## 四、状态说明

| 状态 | 说明 | 验证结果 |
|------|------|----------|
| active | 正常活跃 | valid=true |
| blacklisted | 黑名单，已吊销 | valid=false |
| greylisted | 灰名单，观察中 | valid=true, warning=true |
| inactive | 未激活 | valid=false |
| expired | 已过期 | valid=false |

---

## 五、客户端集成示例代码

### JavaScript 示例
```javascript
async function validateLicense(licenseKey) {
  const response = await fetch('/api/license/validate', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `license_key=${encodeURIComponent(licenseKey)}`
  });
  
  const result = await response.json();
  
  if (!result.valid) {
    // 许可证无效
    if (result.revocation_reason) {
      // 被吊销 - 显示申诉渠道
      showRevokedDialog(result);
    } else {
      // 其他原因无效
      showInvalidLicense(result.message);
    }
    return false;
  }
  
  if (result.warning) {
    // 灰名单 - 显示警告
    showWarningDialog(result);
  }
  
  // 许可证有效，继续运行
  return true;
}

function showRevokedDialog(result) {
  alert(`许可证已被吊销\n\n原因：${result.revocation_reason}\n\n如有疑问，请联系：${result.appeal_channel}`);
}

function showWarningDialog(result) {
  alert(`⚠️ 许可证警告\n\n${result.revocation_reason}\n\n如有疑问，请联系：${result.appeal_channel}`);
}
```

### Python 示例
```python
import requests

def validate_license(license_key):
    url = "https://your-domain.com/api/license/validate"
    params = {"license_key": license_key}
    
    try:
        response = requests.get(url, params=params, timeout=5)
        result = response.json()
        
        if not result["valid"]:
            if "revocation_reason" in result:
                print(f"许可证已被吊销: {result['revocation_reason']}")
                print(f"申诉渠道: {result['appeal_channel']}")
            else:
                print(f"许可证无效: {result['message']}")
            return False
        
        if result.get("warning"):
            print(f"警告: {result['revocation_reason']}")
            print(f"申诉渠道: {result['appeal_channel']}")
        
        return True
        
    except requests.exceptions.RequestException as e:
        print(f"验证失败: {e}")
        return False
```
