<?php include 'includes/header.php'; ?>

<div class="fade-in max-w-2xl mx-auto">
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-bold text-white">Admin Settings</h3>
            <p class="text-sm text-gray-400">Change your admin password.</p>
        </div>
        <div class="p-6 space-y-6">
            <div class="space-y-4">
                <h4 class="text-sm font-bold text-gray-300 uppercase tracking-wide">Change Password</h4>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Current Password</label>
                    <input type="password" id="admin-current-pw" placeholder="••••••••" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">New Password</label>
                    <input type="password" id="admin-new-pw" placeholder="••••••••" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400 mb-1">Confirm New Password</label>
                    <input type="password" id="admin-confirm-pw" placeholder="••••••••" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-200 focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div class="flex justify-end">
                    <button onclick="changeAdminPw()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition-all flex items-center gap-2">
                        <i class="fas fa-lock"></i> Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeAdminPw() {
    const fd = new FormData();
    fd.append('action', 'change_admin_password');
    fd.append('current_password', document.getElementById('admin-current-pw').value);
    fd.append('new_password', document.getElementById('admin-new-pw').value);
    fd.append('confirm_password', document.getElementById('admin-confirm-pw').value);
    fetch('api/admin_api.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) { document.getElementById('admin-current-pw').value = ''; document.getElementById('admin-new-pw').value = ''; document.getElementById('admin-confirm-pw').value = ''; }
        });
}
</script>

<?php include 'includes/footer.php'; ?>
