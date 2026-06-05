<?php
/**
 * License Controller
 */

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/License.php';
require_once __DIR__ . '/../models/User.php';

class LicenseController {
    private $authController;
    private $licenseModel;
    private $userModel;
    
    public function __construct() {
        $this->authController = new AuthController();
        $this->licenseModel = new License();
        $this->userModel = new User();
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
}
