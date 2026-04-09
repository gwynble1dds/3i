<?php include 'includes/header.php'; ?>

<div class="space-y-6 fade-in">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
            class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-sm transition-all hover:border-indigo-500/50 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Currently Active</p>
                    <h3 id="active-count" class="text-3xl font-bold text-white">0</h3>
                </div>
                <div
                    class="p-3 bg-emerald-500/10 rounded-lg text-emerald-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-md text-xl"></i></div>
            </div>
        </div>
        <div
            class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-sm transition-all hover:border-indigo-500/50 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Logs Today</p>
                    <h3 id="total-logs" class="text-3xl font-bold text-white">0</h3>
                </div>
                <div class="p-3 bg-blue-500/10 rounded-lg text-blue-400 group-hover:scale-110 transition-transform"><i
                        class="fas fa-history text-xl"></i></div>
            </div>
        </div>
        <div
            class="bg-gray-800 p-6 rounded-xl border border-gray-700 shadow-sm transition-all hover:border-indigo-500/50 group">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">System Time</p>
                    <h3 id="live-clock" class="text-3xl font-bold text-indigo-400 font-mono">--:--:--</h3>
                </div>
                <div class="p-3 bg-indigo-500/10 rounded-lg text-indigo-400 group-hover:scale-110 transition-transform">
                    <i class="fas fa-clock text-xl"></i></div>
            </div>
        </div>
    </div>


    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-xl">
        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                Live: Currently On Duty
            </h3>
            <button onclick="refreshDutyData()"
                class="p-2 hover:bg-gray-700 rounded-lg text-gray-400 transition-colors">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="p-6">
            <div id="active-duty-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="col-span-full py-10 text-center text-gray-500 italic">Checking for active staff...</div>
            </div>
        </div>
    </div>


    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden shadow-xl">
        <div class="px-6 py-4 bg-gray-900/50 border-b border-gray-700">
            <h3 class="text-lg font-bold text-white">Today's Duty Activity</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-900/30 text-gray-500 text-sm uppercase tracking-wider">
                        <th class="p-4 font-semibold">Staff Name</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold text-center">Time In</th>
                        <th class="p-4 font-semibold text-center">Time Out</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="duty-history-body" class="text-gray-300 divide-y divide-gray-700">

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function refreshDutyData() {

        fetch('api/admin_api.php?action=get_all_duty_logs')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                const activeList = document.getElementById('active-duty-list');
                const historyBody = document.getElementById('duty-history-body');
                const activeCountEl = document.getElementById('active-count');
                const totalLogsEl = document.getElementById('total-logs');

                const logs = data.data;
                const active = logs.filter(l => !l.time_out);

                activeCountEl.innerText = active.length;
                totalLogsEl.innerText = logs.length;


                if (active.length === 0) {
                    activeList.innerHTML = `<div class="col-span-full py-10 text-center text-gray-500 bg-gray-900/20 rounded-xl border border-dashed border-gray-700">No medical staff currently on duty.</div>`;
                } else {
                    activeList.innerHTML = active.map(l => `
                        <div class="bg-gray-750 border border-emerald-500/20 p-4 rounded-xl flex flex-col justify-between hover:border-emerald-500/50 transition-all shadow-lg hover:shadow-emerald-500/5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-400">
                                    <i class="fas fa-user-md text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-white">${l.name}</h4>
                                    <p class="text-xs text-gray-400">${l.role}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-auto">
                                <div class="text-xs text-emerald-300 font-mono bg-emerald-900/30 px-2 py-1 rounded">
                                    In: ${l.time_in}
                                </div>
                                <button onclick="forceEndDuty(${l.id})" class="text-xs bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white px-3 py-1.5 rounded-lg transition-all border border-red-500/30">
                                    Force Out
                                </button>
                            </div>
                        </div>
                    `).join('');
                }


                if (logs.length === 0) {
                    historyBody.innerHTML = `<tr><td colspan="6" class="p-10 text-center text-gray-500 italic">No activity recorded for today.</td></tr>`;
                } else {
                    historyBody.innerHTML = logs.map(l => `
                        <tr class="hover:bg-gray-750 transition-colors">
                            <td class="p-4 font-medium text-white">${l.name}</td>
                            <td class="p-4 text-sm text-gray-400">${l.role}</td>
                            <td class="p-4 text-center text-sm font-mono">${l.time_in}</td>
                            <td class="p-4 text-center text-sm font-mono">${l.time_out || '--:--:--'}</td>
                            <td class="p-4">
                                ${l.time_out
                            ? '<span class="px-2 py-1 rounded-full bg-gray-700 text-gray-400 text-[10px] uppercase font-bold tracking-wider">Completed</span>'
                            : '<span class="px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] uppercase font-bold tracking-wider animate-pulse">Running</span>'
                        }
                            </td>
                            <td class="p-4 text-center">
                                ${!l.time_out
                            ? `<button onclick="forceEndDuty(${l.id})" class="text-red-400 hover:text-red-300 p-2"><i class="fas fa-sign-out-alt"></i></button>`
                            : '<i class="fas fa-check text-emerald-500/50"></i>'
                        }
                            </td>
                        </tr>
                    `).join('');
                }
            });
    }

    function forceEndDuty(logId) {
        if (!confirm('Are you sure you want to manually end this officer\'s duty session?')) return;

        const fd = new FormData();
        fd.append('action', 'force_end_duty');
        fd.append('log_id', logId);

        fetch('api/admin_api.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    refreshDutyData();
                } else {
                    alert(data.message || 'Operation failed');
                }
            });
    }


    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').innerText = now.toLocaleTimeString('en-GB');
    }

    document.addEventListener('DOMContentLoaded', () => {
        refreshDutyData();
        setInterval(refreshDutyData, 30000);
        setInterval(updateClock, 1000);
        updateClock();
    });
</script>

<?php include 'includes/footer.php'; ?>