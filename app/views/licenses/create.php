<?php
$pageTitle = '创建许可证 - 许可证管理平台';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="flex items-center justify-center min-h-[calc(100vh-12rem)]">
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-2xl border border-gray-100">
        <h2 class="text-3xl font-bold mb-8 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            创建新许可证
        </h2>
        
        <form method="POST" action="/licenses/create" class="space-y-6">
            <div>
                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">产品名称 *</label>
                <input 
                    type="text" 
                    id="product_name" 
                    name="product_name" 
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                    placeholder="请输入产品名称"
                >
            </div>
            
            <?php if ($_SESSION['role'] === 'admin' && !empty($users)): ?>
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">分配给用户</label>
                <select 
                    id="user_id" 
                    name="user_id"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                >
                    <option value="<?php echo $_SESSION['user_id']; ?>">当前用户 (<?php echo $_SESSION['username']; ?>)</option>
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username'] . ' (' . $user['email'] . ')'); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php else: ?>
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <?php endif; ?>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">状态</label>
                <select 
                    id="status" 
                    name="status"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                >
                    <option value="active">活跃</option>
                    <option value="inactive">未激活</option>
                    <option value="expired">已过期</option>
                </select>
            </div>
            
            <div>
                <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">过期时间（可选）</label>
                <input 
                    type="date" 
                    id="expires_at" 
                    name="expires_at"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none"
                >
            </div>
            
            <div class="flex space-x-4">
                <button 
                    type="submit"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-[1.02] shadow-lg"
                >
                    创建许可证
                </button>
                <a 
                    href="/dashboard/licenses"
                    class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-all text-center"
                >
                    取消
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
