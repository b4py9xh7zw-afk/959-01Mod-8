<?php
$pageTitle = '许可证管理 - 许可证管理平台';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            许可证管理
        </h1>
        <a href="/licenses/create" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg">
            + 创建许可证
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">许可证密钥</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">产品名称</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">用户</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">过期时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">创建时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($licenses)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">暂无许可证</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($licenses as $license): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="text-sm font-mono text-gray-800"><?php echo htmlspecialchars($license['license_key']); ?></code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-900"><?php echo htmlspecialchars($license['product_name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600"><?php echo htmlspecialchars($license['username'] ?? 'N/A'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                        echo $license['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                            ($license['status'] === 'expired' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'); 
                                    ?>">
                                        <?php 
                                        echo $license['status'] === 'active' ? '活跃' : 
                                            ($license['status'] === 'expired' ? '已过期' : '未激活'); 
                                        ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?php echo $license['expires_at'] ? date('Y-m-d', strtotime($license['expires_at'])) : '永不过期'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    <?php echo date('Y-m-d', strtotime($license['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/licenses/view?id=<?php echo $license['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">查看</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    第 <?php echo $page; ?> 页，共 <?php echo $totalPages; ?> 页
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">上一页</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">下一页</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
