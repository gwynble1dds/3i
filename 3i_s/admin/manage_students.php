<?php include 'includes/header.php'; ?>

<div class="fade-in space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xl font-bold text-white">All Students</h3>
            <p class="text-gray-400 text-sm">View all registered students across all teachers.</p>
        </div>
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
            <input type="text" id="admin-student-search" placeholder="Search students..." onkeyup="filterTable()" class="pl-10 pr-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-gray-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none w-64">
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="admin-students-table">
                <thead><tr class="text-gray-500 text-sm uppercase border-b border-gray-700">
                    <th class="p-4">Student ID</th><th class="p-4">Name</th><th class="p-4">Gender</th><th class="p-4">Age</th><th class="p-4">Grade</th><th class="p-4">Strand</th><th class="p-4">Guardian</th><th class="p-4">Emergency</th>
                </tr></thead>
                <tbody class="text-gray-300 text-sm">
                <?php
                $students = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
                if ($students->num_rows === 0) {
                    echo '<tr><td colspan="8" class="p-8 text-center text-gray-500">No students registered yet</td></tr>';
                }
                while ($s = $students->fetch_assoc()):
                    $gClass = $s['gender'] === 'Male' ? 'bg-blue-800 text-blue-200' : 'bg-pink-800 text-pink-200';
                ?>
                <tr class="border-b border-gray-700 hover:bg-gray-750 searchable-row">
                    <td class="p-4 font-medium text-indigo-300"><?php echo htmlspecialchars($s['student_id']); ?></td>
                    <td class="p-4 font-medium text-white"><?php echo htmlspecialchars($s['name']); ?></td>
                    <td class="p-4"><span class="px-2 py-0.5 rounded text-xs font-medium <?php echo $gClass; ?>"><?php echo $s['gender']; ?></span></td>
                    <td class="p-4"><?php echo $s['age']; ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($s['grade_level']); ?></td>
                    <td class="p-4"><span class="px-2 py-0.5 rounded text-xs bg-purple-800 text-purple-200"><?php echo htmlspecialchars($s['strand']); ?></span></td>
                    <td class="p-4"><?php echo htmlspecialchars($s['guardian_name'] ?? '-'); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($s['emergency_contact'] ?? '-'); ?></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const search = document.getElementById('admin-student-search').value.toLowerCase();
    document.querySelectorAll('.searchable-row').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
    });
}
</script>

<?php include 'includes/footer.php'; ?>
