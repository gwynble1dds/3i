<?php include 'includes/header.php'; ?>

<div class="fade-in space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-white">All Medical Records</h3>
            <p class="text-gray-400 text-sm">View all medical patient records and visits.</p>
        </div>
    </div>


    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php
        $t_patients = $conn->query("SELECT COUNT(*) as c FROM medical_patients")->fetch_assoc()['c'];
        $t_critical = $conn->query("SELECT COUNT(*) as c FROM medical_patients WHERE severity = 'Critical'")->fetch_assoc()['c'];
        $t_visits = $conn->query("SELECT COUNT(*) as c FROM medical_visits")->fetch_assoc()['c'];
        $t_officers = $conn->query("SELECT COUNT(*) as c FROM officers")->fetch_assoc()['c'];
        ?>
        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
            <p class="text-xs text-gray-400 uppercase">Patients</p>
            <h3 class="text-2xl font-bold text-white"><?php echo $t_patients; ?></h3>
        </div>
        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
            <p class="text-xs text-gray-400 uppercase">Critical</p>
            <h3 class="text-2xl font-bold text-red-400"><?php echo $t_critical; ?></h3>
        </div>
        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
            <p class="text-xs text-gray-400 uppercase">Total Visits</p>
            <h3 class="text-2xl font-bold text-blue-400"><?php echo $t_visits; ?></h3>
        </div>
        <div class="bg-gray-800 p-4 rounded-xl border border-gray-700">
            <p class="text-xs text-gray-400 uppercase">Officers</p>
            <h3 class="text-2xl font-bold text-purple-400"><?php echo $t_officers; ?></h3>
        </div>
    </div>


    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-700">
            <h4 class="font-bold text-white">Medical Patients</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-500 text-sm uppercase border-b border-gray-700">
                        <th class="p-4">Patient ID</th>
                        <th class="p-4">Student</th>
                        <th class="p-4">Conditions</th>
                        <th class="p-4">Severity</th>
                        <th class="p-4">Registered</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300 text-sm">
                    <?php
                    $patients = $conn->query("SELECT mp.*, s.name as student_name, s.student_id as stu_id FROM medical_patients mp LEFT JOIN students s ON mp.student_id = s.id ORDER BY mp.created_at DESC");
                    if ($patients->num_rows === 0)
                        echo '<tr><td colspan="5" class="p-8 text-center text-gray-500">No medical patients</td></tr>';
                    while ($p = $patients->fetch_assoc()):
                        $conditions = json_decode($p['conditions'], true) ?: explode(',', $p['conditions']);
                        $badges = implode(' ', array_map(fn($c) => '<span class="px-2 py-0.5 rounded text-xs bg-gray-700 text-gray-300 mr-1">' . htmlspecialchars($c) . '</span>', $conditions));
                        $sevClass = $p['severity'] === 'Critical' ? 'bg-red-800 text-red-200' : 'bg-yellow-800 text-yellow-200';
                        ?>
                        <tr class="border-b border-gray-700 hover:bg-gray-750">
                            <td class="p-4 font-mono text-indigo-300"><?php echo htmlspecialchars($p['patient_id']); ?></td>
                            <td class="p-4">
                                <div class="font-medium text-white">
                                    <?php echo htmlspecialchars($p['student_name'] ?? 'Unknown'); ?></div>
                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($p['stu_id'] ?? ''); ?></div>
                            </td>
                            <td class="p-4"><?php echo $badges; ?></td>
                            <td class="p-4"><span
                                    class="px-2.5 py-0.5 rounded-full text-xs <?php echo $sevClass; ?>"><?php echo $p['severity']; ?></span>
                            </td>
                            <td class="p-4 text-gray-500">
                                <?php echo $p['registered_date'] ? date('M d, Y', strtotime($p['registered_date'])) : '-'; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>