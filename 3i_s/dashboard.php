<?php include 'includes/header.php';

$total_students = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$total_medical = $conn->query("SELECT COUNT(*) as c FROM medical_patients")->fetch_assoc()['c'];
$today = date('Y-m-d');
$active_visits = $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE time_out IS NULL")->fetch_assoc()['c'];
$today_visits = $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE visit_date = '$today'")->fetch_assoc()['c'];
?>

<div class="fade-in space-y-6">
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl p-8 text-white shadow-lg flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="text-emerald-100 opacity-90">Here's what's happening with your students today.</p>
        </div>
        <i class="fas fa-chalkboard-teacher text-6xl text-emerald-200 opacity-50 hidden md:block"></i>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="student_list.php" class="block bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Students</p>
                    <h3 class="text-3xl font-bold text-gray-800"><?php echo $total_students; ?></h3>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg text-blue-600"><i class="fas fa-user-graduate text-xl"></i></div>
            </div>
        </a>
        <a href="medical_records.php" class="block bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">With Medical Conditions</p>
                    <h3 class="text-3xl font-bold text-red-600"><?php echo $total_medical; ?></h3>
                </div>
                <div class="p-3 bg-red-50 rounded-lg text-red-600"><i class="fas fa-heartbeat text-xl"></i></div>
            </div>
        </a>
        <a href="medical_records.php?tab=visits" class="block bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Active Clinic Visits</p>
                    <h3 class="text-3xl font-bold text-orange-600"><?php echo $active_visits; ?></h3>
                </div>
                <div class="p-3 bg-orange-50 rounded-lg text-orange-600"><i class="fas fa-clinic-medical text-xl"></i></div>
            </div>
        </a>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Today's Visits</p>
                    <h3 class="text-3xl font-bold text-emerald-600"><?php echo $today_visits; ?></h3>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600"><i class="fas fa-calendar-day text-xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="student_list.php?action=add" class="bg-emerald-600 hover:bg-emerald-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Add Student
                </a>
                <a href="medical_records.php?tab=visits&action=add" class="bg-red-500 hover:bg-red-600 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Log Clinic Visit
                </a>
                <a href="medical_records.php?action=register" class="bg-blue-500 hover:bg-blue-600 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus"></i> Register Medical
                </a>
            </div>
        </div>

        <div id="clinic-officer-section" class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg">Clinic Officer</h3>
                <i class="fas fa-user-md text-2xl opacity-50"></i>
            </div>
            <div id="duty-officers-list" class="space-y-4">
                <!-- Dynamic duty officers will appear here -->
                <div class="animate-pulse flex space-x-4">
                    <div class="flex-1 space-y-4 py-1">
                        <div class="h-4 bg-purple-400 rounded w-3/4"></div>
                        <div class="space-y-2">
                            <div class="h-4 bg-purple-400 rounded"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recently Added Students</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="p-3 font-semibold border-b">Student ID</th>
                        <th class="p-3 font-semibold border-b">Name</th>
                        <th class="p-3 font-semibold border-b">Grade</th>
                        <th class="p-3 font-semibold border-b">Strand</th>
                        <th class="p-3 font-semibold border-b">Date Added</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php
                    $recent = $conn->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
                    if ($recent->num_rows === 0) {
                        echo '<tr><td colspan="5" class="p-6 text-center text-gray-400">No students yet</td></tr>';
                    }
                    while ($row = $recent->fetch_assoc()):
                        ?>
                        <tr class="hover:bg-gray-50 border-b border-gray-100">
                            <td class="p-3 font-medium"><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="p-3"><?php echo htmlspecialchars($row['grade_level']); ?></td>
                            <td class="p-3"><span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-full text-xs"><?php echo htmlspecialchars($row['strand']); ?></span></td>
                            <td class="p-3 text-gray-500"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateDashboardDuty() {
            fetch('api/medical.php?action=get_duty_officer')
                .then(r => r.json())
                .then(data => {
                    const list = document.getElementById('duty-officers-list');
                    if (data.success && data.on_duty && data.on_duty.length > 0) {
                        list.innerHTML = data.on_duty.map(o => `
                            <div class="officer-entry border-b border-white/10 pb-4 last:border-0 last:pb-0 fade-in">
                                <p class="text-2xl font-bold">${o.name}</p>
                                <p class="text-purple-100 text-sm">${o.role}</p>
                                <div class="mt-2 text-sm text-purple-200">
                                    <i class="fas fa-clock mr-1"></i> Since ${o.time_in}
                                </div>
                            </div>
                        `).join('');
                    } else {
                        list.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-user-slash text-4xl text-purple-300 mb-2"></i>
                                <p class="text-purple-100">No officer on duty</p>
                            </div>
                        `;
                    }
                })
                .catch(err => console.error('Error fetching duty officer:', err));
        }

        // Initial load
        updateDashboardDuty();

        // Refresh every 30 seconds
        setInterval(updateDashboardDuty, 30000);
    });
</script>

<?php include 'includes/footer.php'; ?>