<?php
/**
 * Dashboard Controller
 */

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/License.php';

class DashboardController {
    private $authController;
    private $userModel;
    private $licenseModel;
    
    public function __construct() {
        $this->authController = new AuthController();
        $this->userModel = new User();
        $this->licenseModel = new License();
    }
    
    public function index() {
        $this->authController->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        $stats = [
            'total_licenses' => $this->licenseModel->count(),
            'active_licenses' => $this->licenseModel->countByStatus('active'),
            'expired_licenses' => $this->licenseModel->countByStatus('expired'),
            'total_users' => $this->userModel->count()
        ];
        
        if ($role === 'admin') {
            $recentLicenses = $this->licenseModel->findAll(10, 0);
        } else {
            $recentLicenses = $this->licenseModel->findByUserId($userId, 10, 0);
        }
        
        require_once __DIR__ . '/../views/dashboard/index.php';
    }
    
    public function licenses() {
        $this->authController->requireAuth();
        
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($role === 'admin') {
            $licenses = $this->licenseModel->findAll($limit, $offset);
            $total = $this->licenseModel->count();
        } else {
            $licenses = $this->licenseModel->findByUserId($userId, $limit, $offset);
            $total = $this->licenseModel->countByUserId($userId);
        }
        
        $totalPages = ceil($total / $limit);
        
        require_once __DIR__ . '/../views/dashboard/licenses.php';
    }
    
    public function users() {
        $this->authController->requireAdmin();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->findAll($limit, $offset);
        $total = $this->userModel->count();
        $totalPages = ceil($total / $limit);
        
        require_once __DIR__ . '/../views/dashboard/users.php';
    }
}
