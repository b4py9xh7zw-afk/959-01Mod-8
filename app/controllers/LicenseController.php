<?php
/**
 * License Controller
 */

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/License.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/LicenseRevocationLog.php';

class LicenseController {
    private $authController;
    private $licenseModel;
    private $userModel;
    private $revocationLogModel;
    
    public function __construct() {
        $this->authController = new AuthController();
        $this->licenseModel = new License();
        $this->userModel = new User();
        $this->revocationLogModel = new LicenseRevocationLog();
    }
    
    public function create() {
        $this->authController->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productName = $_POST['product_name'] ?? '';
            $userId = $_POST['user_id'] ?? $_SESSION['user_id'];
            $status = $_POST['status'] ?? 'active';
            $expiresAt = $_POST['expires_at'] ?? null;
            
            if (empty($productName)) {
                $_SESSION['error'] = '产品名称是必填项';
                header('Location: /licenses/create');
                exit;
            }
            
            // Only admins can assign licenses to other users
            if ($userId != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
                $_SESSION['error'] = '访问被拒绝';
                header('Location: /dashboard');
                exit;
            }
            
            try {
                $licenseId = $this->licenseModel->create([
                    'user_id' => $userId,
                    'product_name' => $productName,
                    'status' => $status,
                    'expires_at' => $expiresAt ?: null
                ]);
                
                $_SESSION['success'] = '许可证创建成功';
                header('Location: /licenses/view?id=' . $licenseId);
                exit;
            } catch (Exception $e) {
                error_log("License creation error: " . $e->getMessage());
                $_SESSION['error'] = '创建许可证失败，请重试';
                header('Location: /licenses/create');
                exit;
            }
        }
        
        $users = [];
        if ($_SESSION['role'] === 'admin') {
            $users = $this->userModel->findAll(1000, 0);
        }
        
        require_once __DIR__ . '/../views/licenses/create.php';
    }
    
    public function view() {
        $this->authController->requireAuth();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = '许可证ID是必填项';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $license = $this->licenseModel->findById($id);
        if (!$license) {
            $_SESSION['error'] = '许可证不存在';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        // Users can only view their own licenses unless they're admin
        if ($license['user_id'] != $_SESSION['user_id'] && $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = '访问被拒绝';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        require_once __DIR__ . '/../views/licenses/view.php';
    }
    
    public function update() {
        $this->authController->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = '许可证ID是必填项';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $license = $this->licenseModel->findById($id);
        if (!$license) {
            $_SESSION['error'] = '许可证不存在';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        // Only admins can update licenses
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = '访问被拒绝，需要管理员权限';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        try {
            $data = [];
            if (isset($_POST['product_name'])) {
                $data['product_name'] = $_POST['product_name'];
            }
            if (isset($_POST['status'])) {
                $data['status'] = $_POST['status'];
            }
            if (isset($_POST['expires_at'])) {
                $data['expires_at'] = $_POST['expires_at'] ?: null;
            }
            if (isset($_POST['user_id'])) {
                $data['user_id'] = $_POST['user_id'];
            }
            
            $this->licenseModel->update($id, $data);
            $_SESSION['success'] = '许可证更新成功';
            header('Location: /licenses/view?id=' . $id);
            exit;
        } catch (Exception $e) {
            error_log("License update error: " . $e->getMessage());
            $_SESSION['error'] = '更新许可证失败，请重试';
            header('Location: /licenses/view?id=' . $id);
            exit;
        }
    }
    
    public function delete() {
        $this->authController->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = '许可证ID是必填项';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        try {
            $this->licenseModel->delete($id);
            $_SESSION['success'] = '许可证删除成功';
            header('Location: /dashboard/licenses');
            exit;
        } catch (Exception $e) {
            error_log("License deletion error: " . $e->getMessage());
            $_SESSION['error'] = '删除许可证失败，请重试';
            header('Location: /dashboard/licenses');
            exit;
        }
    }
    
    public function blacklist() {
        $this->authController->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        $appealChannel = $_POST['appeal_channel'] ?? null;
        
        if (!$id || empty($reason)) {
            $_SESSION['error'] = '许可证ID和吊销原因是必填项';
            header('Location: /licenses/view?id=' . $id);
            exit;
        }
        
        $license = $this->licenseModel->findById($id);
        if (!$license) {
            $_SESSION['error'] = '许可证不存在';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        try {
            $previousStatus = $license['status'];
            $result = $this->licenseModel->blacklist($id, $reason, $appealChannel);
            
            if ($result) {
                $this->revocationLogModel->logBlacklist(
                    $id,
                    $_SESSION['user_id'],
                    $reason,
                    $previousStatus
                );
                $_SESSION['success'] = '许可证已加入黑名单';
            } else {
                $_SESSION['error'] = '加入黑名单失败';
            }
        } catch (Exception $e) {
            error_log("Blacklist error: " . $e->getMessage());
            $_SESSION['error'] = '操作失败，请重试';
        }
        
        header('Location: /licenses/view?id=' . $id);
        exit;
    }
    
    public function greylist() {
        $this->authController->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        $appealChannel = $_POST['appeal_channel'] ?? null;
        
        if (!$id || empty($reason)) {
            $_SESSION['error'] = '许可证ID和观察原因是必填项';
            header('Location: /licenses/view?id=' . $id);
            exit;
        }
        
        $license = $this->licenseModel->findById($id);
        if (!$license) {
            $_SESSION['error'] = '许可证不存在';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        try {
            $previousStatus = $license['status'];
            $result = $this->licenseModel->greylist($id, $reason, $appealChannel);
            
            if ($result) {
                $this->revocationLogModel->logGreylist(
                    $id,
                    $_SESSION['user_id'],
                    $reason,
                    $previousStatus
                );
                $_SESSION['success'] = '许可证已加入灰名单观察';
            } else {
                $_SESSION['error'] = '加入灰名单失败';
            }
        } catch (Exception $e) {
            error_log("Greylist error: " . $e->getMessage());
            $_SESSION['error'] = '操作失败，请重试';
        }
        
        header('Location: /licenses/view?id=' . $id);
        exit;
    }
    
    public function restore() {
        $this->authController->requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard/licenses');
            exit;
        }
        
        $id = $_POST['id'] ?? null;
        $reason = $_POST['reason'] ?? '';
        $restoreScope = $_POST['restore_scope'] ?? '';
        $responsiblePerson = $_POST['responsible_person'] ?? '';
        
        if (!$id || empty($reason) || empty($restoreScope) || empty($responsiblePerson)) {
            $_SESSION['error'] = '所有字段都是必填项';
            header('Location: /licenses/view?id=' . $id);
            exit;
        }
        
        $license = $this->licenseModel->findById($id);
        if (!$license) {
            $_SESSION['error'] = '许可证不存在';
            header('Location: /dashboard/licenses');
            exit;
        }
        
        if (!in_array($license['status'], ['blacklisted', 'greylisted'])) {
            $_SESSION['error'] = '该许可证未被吊销，无需恢复';
            header('Location: /licenses/view?id=' . $id);
            exit;
        }
        
        try {
            $previousStatus = $license['status'];
            $result = $this->licenseModel->restore($id);
            
            if ($result) {
                $this->revocationLogModel->logRestore(
                    $id,
                    $_SESSION['user_id'],
                    $reason,
                    $restoreScope,
                    $responsiblePerson,
                    $previousStatus
                );
                $_SESSION['success'] = '许可证已恢复，恢复记录已保存';
            } else {
                $_SESSION['error'] = '恢复失败';
            }
        } catch (Exception $e) {
            error_log("Restore error: " . $e->getMessage());
            $_SESSION['error'] = '操作失败，请重试';
        }
        
        header('Location: /licenses/view?id=' . $id);
        exit;
    }
    
    public function revocationLogs() {
        $this->authController->requireAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $actionType = $_GET['type'] ?? null;
        
        if ($actionType) {
            $logs = $this->revocationLogModel->findByActionType($actionType, $limit, $offset);
            $total = $this->revocationLogModel->countByActionType($actionType);
        } else {
            $logs = $this->revocationLogModel->findAll($limit, $offset);
            $total = $this->revocationLogModel->count();
        }
        
        $totalPages = ceil($total / $limit);
        
        $stats = [
            'total' => $this->revocationLogModel->count(),
            'blacklist' => $this->revocationLogModel->countByActionType('blacklist'),
            'greylist' => $this->revocationLogModel->countByActionType('greylist'),
            'restore' => $this->revocationLogModel->countByActionType('restore')
        ];
        
        require_once __DIR__ . '/../views/licenses/revocation_logs.php';
    }
    
    public function apiValidate() {
        header('Content-Type: application/json');
        
        $licenseKey = $_POST['license_key'] ?? $_GET['license_key'] ?? '';
        
        if (empty($licenseKey)) {
            echo json_encode([
                'valid' => false,
                'message' => 'License key is required'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        try {
            $result = $this->licenseModel->validate($licenseKey);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("API validate error: " . $e->getMessage());
            echo json_encode([
                'valid' => false,
                'message' => 'Validation failed'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}
