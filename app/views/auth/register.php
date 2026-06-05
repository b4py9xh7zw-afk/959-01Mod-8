<?php
$pageTitle = '注册 - 许可证管理平台';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-center min-h-[calc(100vh-12rem)]">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md border border-gray-100">
        <h2 class="text-3xl font-bold text-center mb-8 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            注册
        </h2>
        
        <form method="POST" action="/auth/register" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">用户名</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请输入用户名"
                >
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">邮箱</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请输入邮箱"
                >
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">密码</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    minlength="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请输入密码（至少6个字符）"
                >
            </div>
            
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">确认密码</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required
                    minlength="6"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请再次输入密码"
                >
            </div>
            
            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-[1.02] shadow-lg"
            >
                注册
            </button>
        </form>
        
        <p class="mt-6 text-center text-gray-600">
            已有账号？ 
            <a href="/auth/login" class="text-blue-600 hover:text-blue-700 font-semibold">立即登录</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
