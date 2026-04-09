<?php include 'includes/header.php'; ?>

<div class="fade-in space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-white">Pending Account Approvals</h3>
            <p class="text-gray-400 text-sm">Approve or reject teacher registrations using their unique codes.</p>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead><tr class="text-gray-500 text-sm uppercase border-b border-gray-700">
                    <th class="p-4">Username</th><th class="p-4">Email</th><th class="p-4">Unique Code</th><th class="p-4">Status</th><th class="p-4">Registered</th><th class="p-4 text-right">Actions</th>
                </tr></thead>
                <tbody id="users-table" class="text-gray-300 text-sm"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadUsers);

function loadUsers() {
    fetch('api/admin_api.php?action=list_users').then(r => r.json()).then(data => {
        const tbody = document.getElementById('users-table');
        if (data.data.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-500">No accounts found</td></tr>'; return; }
        tbody.innerHTML = data.data.map(u => {
            const sClass = u.status === 'approved' ? 'bg-green-800 text-green-200' : (u.status === 'pending' ? 'bg-yellow-800 text-yellow-200' : 'bg-red-800 text-red-200');
            const actions = u.status === 'pending' ? `
                <button onclick="approveUser(${u.id})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-md text-xs font-medium mr-2 transition-all"><i class="fas fa-check mr-1"></i>Approve</button>
                <button onclick="rejectUser(${u.id})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-all"><i class="fas fa-times mr-1"></i>Reject</button>
            ` : `<span class="text-gray-500 text-xs">${u.status === 'approved' ? '<i class="fas fa-check text-green-400"></i> Approved' : '<i class="fas fa-times text-red-400"></i> Rejected'}</span>`;
            return `<tr class="border-b border-gray-700 hover:bg-gray-750">
                <td class="p-4 font-medium text-white">${u.username}</td>
                <td class="p-4">${u.email}</td>
                <td class="p-4 font-mono text-indigo-300 font-bold tracking-wider">${u.unique_code}</td>
                <td class="p-4"><span class="px-2.5 py-0.5 rounded-full text-xs font-medium ${sClass}">${u.status.charAt(0).toUpperCase() + u.status.slice(1)}</span></td>
                <td class="p-4 text-gray-500">${new Date(u.created_at).toLocaleDateString()}</td>
                <td class="p-4 text-right">${actions}</td>
            </tr>`;
        }).join('');
    });
}

function approveUser(id) {
    if (!confirm('Approve this account?')) return;
    const fd = new FormData(); fd.append('action', 'approve_user'); fd.append('id', id);
    fetch('api/admin_api.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => { showToast(data.message, data.success ? 'success' : 'error'); loadUsers(); });
}

function rejectUser(id) {
    if (!confirm('Reject this account?')) return;
    const fd = new FormData(); fd.append('action', 'reject_user'); fd.append('id', id);
    fetch('api/admin_api.php', { method: 'POST', body: fd }).then(r => r.json()).then(data => { showToast(data.message, data.success ? 'success' : 'error'); loadUsers(); });
}
</script>

<?php include 'includes/footer.php'; ?>
