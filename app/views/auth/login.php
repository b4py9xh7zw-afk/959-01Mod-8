<?php
$pageTitle = '登录 - 许可证管理平台';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Override main container max-width for login page */
    main.max-w-7xl {
        max-width: 100% !important;
    }
</style>

<div class="flex items-center justify-center min-h-full py-12 px-4">
    <div class="bg-white rounded-2xl shadow-xl p-10 w-full border border-gray-100" style="max-width: 1000px !important; min-width: 600px !important; width: 90% !important;">
        <h2 class="text-4xl font-bold text-center mb-10 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            登录
        </h2>
        
        <form method="POST" action="/auth/login" class="space-y-7">
            <div>
                <label for="email" class="block text-base font-medium text-gray-700 mb-3">邮箱</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    class="w-full px-5 py-4 text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请输入您的邮箱"
                >
            </div>
            
            <div>
                <label for="password" class="block text-base font-medium text-gray-700 mb-3">密码</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    class="w-full px-5 py-4 text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请输入您的密码"
                >
            </div>
            
            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 text-lg rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-[1.02] shadow-lg"
            >
                登录
            </button>
        </form>
        
        <p class="mt-8 text-center text-gray-600 text-base">
            还没有账号？ 
            <a href="/auth/register" class="text-blue-600 hover:text-blue-700 font-semibold">立即注册</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
