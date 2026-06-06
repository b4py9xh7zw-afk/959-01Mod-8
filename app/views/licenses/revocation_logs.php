<?php
$pageTitle = '吊销操作日志 - 许可证管理平台';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            吊销操作日志
        </h1>
        <a href="/dashboard/licenses" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
            ← 返回许可证列表
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="text-sm text-gray-500 mb-1">总操作数</div>
            <div class="text-3xl font-bold text-gray-800"><?php echo $stats['total']; ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="text-sm text-gray-500 mb-1">加入黑名单</div>
            <div class="text-3xl font-bold text-black"><?php echo $stats['blacklist']; ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="text-sm text-gray-500 mb-1">加入灰名单</div>
            <div class="text-3xl font-bold text-yellow-600"><?php echo $stats['greylist']; ?></div>
        </div>
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="text-sm text-gray-500 mb-1">恢复许可</div>
            <div class="text-3xl font-bold text-green-600"><?php echo $stats['restore']; ?></div>
        </div>
    </div>
    
    <div class="flex space-x-2">
        <a href="/licenses/revocation-logs" class="px-4 py-2 <?php echo !$actionType ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700'; ?> rounded-lg hover:bg-blue-600 transition-colors">
            全部
        </a>
        <a href="/licenses/revocation-logs?type=blacklist" class="px-4 py-2 <?php echo $actionType === 'blacklist' ? 'bg-black text-white' : 'bg-gray-200 text-gray-700'; ?> rounded-lg hover:bg-gray-800 transition-colors">
            黑名单
        </a>
        <a href="/licenses/revocation-logs?type=greylist" class="px-4 py-2 <?php echo $actionType === 'greylist' ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-700'; ?> rounded-lg hover:bg-yellow-600 transition-colors">
            灰名单
        </a>
        <a href="/licenses/revocation-logs?type=restore" class="px-4 py-2 <?php echo $actionType === 'restore' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'; ?> rounded-lg hover:bg-green-600 transition-colors">
            恢复记录
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">时间</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作类型</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">许可证密钥</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作人</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">原因</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">恢复范围/负责人</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状态变更</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">暂无操作记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo date('Y-m-d H:i:s', strtotime($log['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php 
                                        echo $log['action_type'] === 'blacklist' ? 'bg-black text-white' : 
                                            ($log['action_type'] === 'greylist' ? 'bg-yellow-100 text-yellow-800' : 
                                            ($log['action_type'] === 'restore' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')); 
                                    ?>">
                                        <?php 
                                        echo $log['action_type'] === 'blacklist' ? '加入黑名单' : 
                                            ($log['action_type'] === 'greylist' ? '加入灰名单' : 
                                            ($log['action_type'] === 'restore' ? '恢复许可' : $log['action_type'])); 
                                        ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="text-sm font-mono text-gray-800"><?php echo htmlspecialchars($log['license_key']); ?></code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo htmlspecialchars($log['operator_name'] ?? 'N/A'); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                    <?php echo htmlspecialchars(mb_substr($log['reason'] ?? '', 0, 50)); ?>
                                    <?php if (mb_strlen($log['reason'] ?? '') > 50) echo '...'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php if ($log['action_type'] === 'restore'): ?>
                                        <div class="text-xs">
                                            <div><span class="font-medium">范围：</span><?php echo htmlspecialchars($log['restore_scope'] ?? 'N/A'); ?></div>
                                            <div><span class="font-medium">负责人：</span><?php echo htmlspecialchars($log['responsible_person'] ?? 'N/A'); ?></div>
                                        </div>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <?php if ($log['previous_status'] && $log['new_status']): ?>
                                        <span class="<?php 
                                            echo $log['previous_status'] === 'blacklisted' ? 'text-black' : 
                                                ($log['previous_status'] === 'greylisted' ? 'text-yellow-600' : 'text-green-600'); 
                                        ?>">
                                            <?php 
                                            echo $log['previous_status'] === 'active' ? '活跃' : 
                                                ($log['previous_status'] === 'blacklisted' ? '黑名单' : 
                                                ($log['previous_status'] === 'greylisted' ? '灰名单' : $log['previous_status'])); 
                                            ?>
                                        </span>
                                        →
                                        <span class="<?php 
                                            echo $log['new_status'] === 'blacklisted' ? 'text-black font-medium' : 
                                                ($log['new_status'] === 'greylisted' ? 'text-yellow-600 font-medium' : 
                                                ($log['new_status'] === 'active' ? 'text-green-600 font-medium' : 'text-gray-600')); 
                                        ?>">
                                            <?php 
                                            echo $log['new_status'] === 'active' ? '活跃' : 
                                                ($log['new_status'] === 'blacklisted' ? '黑名单' : 
                                                ($log['new_status'] === 'greylisted' ? '灰名单' : $log['new_status'])); 
                                            ?>
                                        </span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
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
                        <a href="?page=<?php echo $page - 1; ?><?php echo $actionType ? '&type=' . $actionType : ''; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">上一页</a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $actionType ? '&type=' . $actionType : ''; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">下一页</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
