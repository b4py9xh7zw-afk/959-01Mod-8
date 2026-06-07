<?php
$pageTitle = '许可证详情 - 许可证管理平台';
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
            许可证详情
        </h1>
        <a href="/dashboard/licenses" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
            ← 返回许可证列表
        </a>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8">
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">许可证密钥</label>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <code class="text-lg font-mono text-gray-800"><?php echo htmlspecialchars($license['license_key']); ?></code>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">状态</label>
                    <div class="mt-2">
                        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full <?php 
                            echo $license['status'] === 'active' ? 'bg-green-100 text-green-800' : 
                                ($license['status'] === 'expired' ? 'bg-red-100 text-red-800' : 
                                ($license['status'] === 'blacklisted' ? 'bg-black text-white' : 
                                ($license['status'] === 'greylisted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))); 
                        ?>">
                            <?php 
                            echo $license['status'] === 'active' ? '活跃' : 
                                ($license['status'] === 'expired' ? '已过期' : 
                                ($license['status'] === 'blacklisted' ? '黑名单' : 
                                ($license['status'] === 'greylisted' ? '灰名单' : '未激活'))); 
                            ?>
                        </span>
                    </div>
                </div>
                
                <?php if (in_array($license['status'], ['blacklisted', 'greylisted'])): ?>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-2">吊销原因</label>
                    <p class="text-lg text-gray-800 bg-gray-50 p-3 rounded-lg border border-gray-200"><?php echo htmlspecialchars($license['revocation_reason'] ?? 'N/A'); ?></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-2">申诉渠道</label>
                    <p class="text-lg text-blue-600 bg-blue-50 p-3 rounded-lg border border-blue-200"><?php echo htmlspecialchars($license['appeal_channel'] ?? 'N/A'); ?></p>
                </div>
                <?php endif; ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">产品名称</label>
                    <p class="text-lg text-gray-800"><?php echo htmlspecialchars($license['product_name']); ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">分配用户</label>
                    <p class="text-lg text-gray-800"><?php echo htmlspecialchars($license['username'] ?? 'N/A'); ?></p>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($license['email'] ?? ''); ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">创建时间</label>
                    <p class="text-lg text-gray-800"><?php echo date('Y-m-d H:i:s', strtotime($license['created_at'])); ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-2">过期时间</label>
                    <p class="text-lg text-gray-800">
                        <?php echo $license['expires_at'] ? date('Y-m-d H:i:s', strtotime($license['expires_at'])) : '永不过期'; ?>
                    </p>
                </div>
            </div>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">管理员操作</h3>
                <div class="flex flex-wrap gap-3">
                    <button 
                        onclick="document.getElementById('updateForm').classList.toggle('hidden')"
                        class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                    >
                        编辑许可证
                    </button>
                    
                    <?php if (!in_array($license['status'], ['blacklisted', 'greylisted'])): ?>
                    <button 
                        onclick="document.getElementById('blacklistForm').classList.toggle('hidden')"
                        class="px-6 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors"
                    >
                        加入黑名单
                    </button>
                    <button 
                        onclick="document.getElementById('greylistForm').classList.toggle('hidden')"
                        class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                    >
                        加入灰名单
                    </button>
                    <?php endif; ?>
                    
                    <?php if (in_array($license['status'], ['blacklisted', 'greylisted'])): ?>
                    <button 
                        onclick="document.getElementById('restoreForm').classList.toggle('hidden')"
                        class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                    >
                        恢复许可证
                    </button>
                    <?php endif; ?>
                    
                    <form method="POST" action="/licenses/delete" onsubmit="return confirm('确定要删除此许可证吗？');" class="inline">
                        <input type="hidden" name="id" value="<?php echo $license['id']; ?>">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
                        >
                            删除许可证
                        </button>
                    </form>
                </div>
                
                <form id="updateForm" method="POST" action="/licenses/update" class="hidden mt-6 space-y-4 bg-gray-50 p-6 rounded-lg">
                    <input type="hidden" name="id" value="<?php echo $license['id']; ?>">
                    
                    <div>
                        <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">产品名称</label>
                        <input 
                            type="text" 
                            id="product_name" 
                            name="product_name" 
                            value="<?php echo htmlspecialchars($license['product_name']); ?>"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">状态</label>
                        <select 
                            id="status" 
                            name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="active" <?php echo $license['status'] === 'active' ? 'selected' : ''; ?>>活跃</option>
                            <option value="inactive" <?php echo $license['status'] === 'inactive' ? 'selected' : ''; ?>>未激活</option>
                            <option value="expired" <?php echo $license['status'] === 'expired' ? 'selected' : ''; ?>>已过期</option>
                        </select>
                        <?php if (in_array($license['status'], ['blacklisted', 'greylisted'])): ?>
                        <p class="text-yellow-600 text-sm mt-2">
                            ⚠️ 当前许可证状态为「<?php echo $license['status'] === 'blacklisted' ? '黑名单' : '灰名单'; ?>」，如需修改状态请使用下方的「恢复许可证」功能。
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">过期时间</label>
                        <input 
                            type="date" 
                            id="expires_at" 
                            name="expires_at"
                            value="<?php echo $license['expires_at'] ? date('Y-m-d', strtotime($license['expires_at'])) : ''; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div class="flex space-x-4">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                        >
                            更新许可证
                        </button>
                        <button 
                            type="button"
                            onclick="document.getElementById('updateForm').classList.add('hidden')"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            取消
                        </button>
                    </div>
                </form>
                
                <form id="blacklistForm" method="POST" action="/licenses/blacklist" class="hidden mt-6 space-y-4 bg-gray-900 p-6 rounded-lg text-white" onsubmit="return confirm('确定要将此许可证加入黑名单吗？该操作将立即停用此许可证。');">
                    <input type="hidden" name="id" value="<?php echo $license['id']; ?>">
                    
                    <h4 class="text-xl font-semibold text-white">加入黑名单</h4>
                    <p class="text-gray-300 text-sm">将许可证加入黑名单后，该许可证将立即失效，客户端软件将无法使用。</p>
                    
                    <div>
                        <label for="blacklist_reason" class="block text-sm font-medium text-gray-300 mb-2">吊销原因 <span class="text-red-400">*</span></label>
                        <textarea 
                            id="blacklist_reason" 
                            name="reason" 
                            required
                            rows="3"
                            placeholder="例如：发现泄露授权码、异常批量激活、盗版镜像等"
                            class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-800 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        ></textarea>
                    </div>
                    
                    <div>
                        <label for="blacklist_appeal" class="block text-sm font-medium text-gray-300 mb-2">申诉渠道</label>
                        <input 
                            type="text" 
                            id="blacklist_appeal" 
                            name="appeal_channel" 
                            placeholder="例如：support@example.com 或 400-888-8888"
                            class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-gray-800 text-white focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                        <p class="text-gray-400 text-xs mt-1">留空将使用默认申诉渠道：support@example.com</p>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        >
                            确认加入黑名单
                        </button>
                        <button 
                            type="button"
                            onclick="document.getElementById('blacklistForm').classList.add('hidden')"
                            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                        >
                            取消
                        </button>
                    </div>
                </form>
                
                <form id="greylistForm" method="POST" action="/licenses/greylist" class="hidden mt-6 space-y-4 bg-yellow-50 p-6 rounded-lg border border-yellow-200" onsubmit="return confirm('确定要将此许可证加入灰名单观察吗？');">
                    <input type="hidden" name="id" value="<?php echo $license['id']; ?>">
                    
                    <h4 class="text-xl font-semibold text-yellow-800">加入灰名单观察</h4>
                    <p class="text-yellow-700 text-sm">将许可证加入灰名单后，许可证仍然可以使用，但客户端会显示警告信息。适用于需要进一步观察确认的情况。</p>
                    
                    <div>
                        <label for="greylist_reason" class="block text-sm font-medium text-yellow-800 mb-2">观察原因 <span class="text-red-600">*</span></label>
                        <textarea 
                            id="greylist_reason" 
                            name="reason" 
                            required
                            rows="3"
                            placeholder="例如：可疑激活行为、需要进一步核实等"
                            class="w-full px-4 py-2 border border-yellow-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        ></textarea>
                    </div>
                    
                    <div>
                        <label for="greylist_appeal" class="block text-sm font-medium text-yellow-800 mb-2">申诉渠道</label>
                        <input 
                            type="text" 
                            id="greylist_appeal" 
                            name="appeal_channel"
                            placeholder="例如：support@example.com 或 400-888-8888"
                            class="w-full px-4 py-2 border border-yellow-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        >
                        <p class="text-yellow-600 text-xs mt-1">留空将使用默认申诉渠道：support@example.com</p>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors"
                        >
                            确认加入灰名单
                        </button>
                        <button 
                            type="button"
                            onclick="document.getElementById('greylistForm').classList.add('hidden')"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            取消
                        </button>
                    </div>
                </form>
                
                <form id="restoreForm" method="POST" action="/licenses/restore" class="hidden mt-6 space-y-4 bg-green-50 p-6 rounded-lg border border-green-200" onsubmit="return confirm('确定要恢复此许可证吗？请确保已填写完整的恢复记录。');">
                    <input type="hidden" name="id" value="<?php echo $license['id']; ?>">
                    
                    <h4 class="text-xl font-semibold text-green-800">恢复许可证</h4>
                    <p class="text-green-700 text-sm">恢复后许可证将重新激活。请务必填写完整的恢复记录，包括恢复范围和负责人，以便审计追踪。</p>
                    
                    <div>
                        <label for="restore_reason" class="block text-sm font-medium text-green-800 mb-2">恢复原因 <span class="text-red-600">*</span></label>
                        <textarea 
                            id="restore_reason" 
                            name="reason" 
                            required
                            rows="2"
                            placeholder="例如：误封、经核实无违规行为等"
                            class="w-full px-4 py-2 border border-green-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        ></textarea>
                    </div>
                    
                    <div>
                        <label for="restore_scope" class="block text-sm font-medium text-green-800 mb-2">恢复范围 <span class="text-red-600">*</span></label>
                        <input 
                            type="text" 
                            id="restore_scope" 
                            name="restore_scope" 
                            required
                            placeholder="例如：仅本许可证、该用户所有许可证、全部误封许可证等"
                            class="w-full px-4 py-2 border border-green-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div>
                        <label for="responsible_person" class="block text-sm font-medium text-green-800 mb-2">负责人 <span class="text-red-600">*</span></label>
                        <input 
                            type="text" 
                            id="responsible_person" 
                            name="responsible_person" 
                            required
                            placeholder="例如：张三（安全主管）"
                            class="w-full px-4 py-2 border border-green-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                    </div>
                    
                    <div class="flex space-x-4">
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors"
                        >
                            确认恢复
                        </button>
                        <button 
                            type="button"
                            onclick="document.getElementById('restoreForm').classList.add('hidden')"
                            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors"
                        >
                            取消
                        </button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
