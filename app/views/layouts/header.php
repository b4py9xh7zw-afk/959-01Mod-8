<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? '许可证管理平台'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex flex-col">
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        许可证管理平台
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="text-gray-700">欢迎，<strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
                        <a href="/dashboard" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">仪表板</a>
                        <a href="/dashboard/licenses" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">许可证</a>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="/dashboard/users" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">用户管理</a>
                        <?php endif; ?>
                        <a href="/auth/logout" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">退出</a>
                    <?php else: ?>
                        <a href="/auth/login" class="px-4 py-2 text-gray-700 hover:text-blue-600 transition-colors">登录</a>
                        <a href="/auth/register" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">注册</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow mb-16">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>
