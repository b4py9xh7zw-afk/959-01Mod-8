<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        // Session is already started in public/index.php
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $_SESSION['error'] = '请填写所有字段';
                header('Location: /auth/login');
                exit;
            }
            
            if ($this->userModel->verifyPassword($email, $password)) {
                $user = $this->userModel->findByEmail($email);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success'] = '登录成功';
                
                header('Location: /dashboard');
                exit;
            } else {
                $_SESSION['error'] = '邮箱或密码错误';
                header('Location: /auth/login');
                exit;
            }
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($username) || empty($email) || empty($password)) {
                $_SESSION['error'] = '请填写所有字段';
                header('Location: /auth/register');
                exit;
            }
            
            if ($password !== $confirmPassword) {
                $_SESSION['error'] = '两次输入的密码不一致';
                header('Location: /auth/register');
                exit;
            }
            
            if (strlen($password) < 6) {
                $_SESSION['error'] = '密码长度至少为6个字符';
                header('Location: /auth/register');
                exit;
            }
            
            if ($this->userModel->findByEmail($email)) {
                $_SESSION['error'] = '该邮箱已被注册';
                header('Location: /auth/register');
                exit;
            }
            
            try {
                $this->userModel->create([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role' => 'user'
                ]);
                
                $_SESSION['success'] = '注册成功，请登录';
                header('Location: /auth/login');
                exit;
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                $_SESSION['error'] = '注册失败，请重试';
                header('Location: /auth/register');
                exit;
            }
        }
        
        require_once __DIR__ . '/../views/auth/register.php';
    }
    
    public function logout() {
        session_destroy();
        header('Location: /auth/login');
        exit;
    }
    
    public function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = '访问被拒绝，需要管理员权限';
            header('Location: /dashboard');
            exit;
        }
    }
}
