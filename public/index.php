<?php
/**
 * Main Entry Point
 * Simple router for the application
 */

// Start output buffering to prevent header issues
ob_start();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
require_once __DIR__ . '/../app/config/database.php';

// Simple routing
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove leading and trailing slashes
$path = trim($path, '/');

// Route mapping
$routes = [
    '' => ['controller' => 'DashboardController', 'action' => 'index'],
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    'dashboard/licenses' => ['controller' => 'DashboardController', 'action' => 'licenses'],
    'dashboard/users' => ['controller' => 'DashboardController', 'action' => 'users'],
    'auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
    'auth/register' => ['controller' => 'AuthController', 'action' => 'register'],
    'auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'licenses/create' => ['controller' => 'LicenseController', 'action' => 'create'],
    'licenses/view' => ['controller' => 'LicenseController', 'action' => 'view'],
    'licenses/update' => ['controller' => 'LicenseController', 'action' => 'update'],
    'licenses/delete' => ['controller' => 'LicenseController', 'action' => 'delete'],
];

// Default route
if (empty($path)) {
    $path = '';
}

// Find route
$route = $routes[$path] ?? null;

if (!$route) {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <title>404 - 页面未找到</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            <p class="text-xl text-gray-600 mb-8">页面未找到</p>
            <a href="/dashboard" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">返回仪表板</a>
        </div>
    </body>
    </html>';
    exit;
}

// Load controller
$controllerName = $route['controller'];
$actionName = $route['action'];

$controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    error_log("Controller file not found: {$controllerFile}");
    http_response_code(500);
    echo 'Internal Server Error';
    exit;
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    error_log("Controller class not found: {$controllerName}");
    http_response_code(500);
    echo 'Internal Server Error';
    exit;
}

// Instantiate and call action
try {
    $controller = new $controllerName();
    if (!method_exists($controller, $actionName)) {
        error_log("Action method not found: {$controllerName}::{$actionName}");
        http_response_code(500);
        echo 'Internal Server Error';
        exit;
    }
    $controller->$actionName();
} catch (Exception $e) {
    error_log("Error executing controller: " . $e->getMessage());
    http_response_code(500);
    echo '<!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <title>500 - 服务器错误</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-red-600 mb-4">500</h1>
            <p class="text-xl text-gray-600 mb-8">服务器内部错误</p>
            <a href="/dashboard" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">返回仪表板</a>
        </div>
    </body>
    </html>';
    exit;
}
