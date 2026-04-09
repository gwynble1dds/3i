<?php include 'includes/header.php';

$total_teachers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'approved'")->fetch_assoc()['c'];
$total_students = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$total_medical = $conn->query("SELECT COUNT(*) as c FROM medical_patients")->fetch_assoc()['c'];
$total_visits = $conn->query("SELECT COUNT(*) as c FROM medical_visits")->fetch_assoc()['c'];
?>

<div class="fade-in space-y-6">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-8 text-white shadow-lg">
        <h1 class="text-3xl font-bold mb-2">Admin Dashboard</h1>
        <p class="text-indigo-200 opacity-90">System overview and management.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-indigo-500 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Total Teachers</p>
                    <h3 class="text-3xl font-bold text-white"><?php echo $total_teachers; ?></h3>
                </div>
                <div class="p-3 bg-indigo-900 rounded-lg text-indigo-400"><i
                        class="fas fa-chalkboard-teacher text-xl"></i></div>
            </div>
            <div class="mt-4 flex gap-4 text-xs">
                <span class="text-yellow-400"><i class="fas fa-clock mr-1"></i><?php echo $pending; ?> Pending</span>
                <span class="text-green-400"><i class="fas fa-check mr-1"></i><?php echo $approved; ?> Approved</span>
            </div>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-blue-500 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Total Students</p>
                    <h3 class="text-3xl font-bold text-white"><?php echo $total_students; ?></h3>
                </div>
                <div class="p-3 bg-blue-900 rounded-lg text-blue-400"><i class="fas fa-user-graduate text-xl"></i></div>
            </div>
        </div>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700 hover:border-red-500 transition-all">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Medical Records</p>
                    <h3 class="text-3xl font-bold text-white"><?php echo $total_medical; ?></h3>
                </div>
                <div class="p-3 bg-red-900 rounded-lg text-red-400"><i class="fas fa-heartbeat text-xl"></i></div>
            </div>
            <p class="mt-4 text-xs text-gray-500"><?php echo $total_visits; ?> total clinic visits</p>
        </div>
    </div>


    <!-- Duty Officers Container -->
    <div id="duty-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Dynamic duty cards will be inserted here -->
    </div>

    <!-- No Duty Fallback -->
    <div id="no-duty-card" class="hidden">
        <div class="bg-gray-800/60 border border-gray-700/50 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-700/50 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-md text-gray-500 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500 font-medium">On Duty Today</p>
                    <h3 class="text-lg font-bold text-gray-400 mt-1">No officer currently on duty</h3>
                    <p class="text-sm text-gray-500 mt-0.5">No active officer log for today.</p>
                </div>
            </div>
        </div>
    </div>

    <?php if ($pending > 0): ?>
        <div class="bg-yellow-900/30 border border-yellow-600/30 rounded-xl p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-500/20 rounded-full"><i
                        class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i></div>
                <div class="flex-1">
                    <h3 class="text-yellow-300 font-bold"><?php echo $pending; ?> Pending Account(s)</h3>
                    <p class="text-yellow-500 text-sm">Teacher accounts waiting for approval.</p>
                </div>
                <a href="approve_users.php"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-5 py-2 rounded-lg font-medium transition-all">Review
                    Now</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
        <h3 class="text-lg font-bold text-white mb-4">Recent Teacher Accounts</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-sm uppercase border-b border-gray-700">
                        <th class="p-3">Username</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Unique Code</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Registered</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300 text-sm">
                    <?php
                    $teachers = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
                    while ($t = $teachers->fetch_assoc()):
                        $s_class = $t['status'] === 'approved' ? 'bg-green-800 text-green-200' : ($t['status'] === 'pending' ? 'bg-yellow-800 text-yellow-200' : 'bg-red-800 text-red-200');
                        ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-750">
                            <td class="p-3 font-medium"><?php echo htmlspecialchars($t['username']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($t['email']); ?></td>
                            <td class="p-3 font-mono text-indigo-300"><?php echo htmlspecialchars($t['unique_code']); ?>
                            </td>
                            <td class="p-3"><span
                                    class="px-2 py-0.5 rounded-full text-xs font-medium <?php echo $s_class; ?>"><?php echo ucfirst($t['status']); ?></span>
                            </td>
                            <td class="p-3 text-gray-500"><?php echo date('M d, Y', strtotime($t['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateDutyStatus() {
            fetch('api/admin_api.php?action=get_duty_officer')
                .then(r => r.json())
                .then(data => {
                    const dutyContainer = document.getElementById('duty-container');
                    const noDutyCard = document.getElementById('no-duty-card');
                    
                    if (data.success && data.on_duty.length > 0) {
                        noDutyCard.classList.add('hidden');
                        dutyContainer.innerHTML = data.on_duty.map(officer => `
                            <div class="bg-gradient-to-r from-emerald-900/40 to-teal-900/40 border border-emerald-600/30 rounded-xl p-6 fade-in">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-emerald-600/30 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user-md text-emerald-400 text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm text-emerald-400 font-medium flex items-center gap-2">
                                            <span class="w-2 h-2 bg-emerald-400 rounded-full inline-block" style="animation: badgePulse 1.5s ease-in-out infinite;"></span>
                                            On Duty
                                        </p>
                                        <h3 class="text-xl font-bold text-white mt-1">${officer.name}</h3>
                                        <p class="text-sm text-emerald-300/80 mt-0.5">${officer.role}</p>
                                    </div>
                                    <div class="text-right hidden sm:block">
                                        <p class="text-xs text-gray-500">In: ${officer.time_in}</p>
                                        <p class="text-xs text-emerald-400/60 font-mono">ID: ${officer.id || ''}</p>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        dutyContainer.innerHTML = '';
                        noDutyCard.classList.remove('hidden');
                    }
                });
        }
        
        updateDutyStatus();
        setInterval(updateDutyStatus, 30000); // Update every 30s
    });
</script>

<?php include 'includes/footer.php'; ?>