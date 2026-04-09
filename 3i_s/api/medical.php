<?php
require_once __DIR__ . '/../db_config.php';
requireLogin();

header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

    case 'list_patients':
        $filter = sanitize($conn, $_GET['filter'] ?? 'all');
        $sql = "SELECT mp.*, s.name as student_name, s.student_id as stu_id, s.gender, s.age 
                FROM medical_patients mp 
                LEFT JOIN students s ON mp.student_id = s.id";
        if ($filter !== 'all')
            $sql .= " WHERE mp.conditions LIKE '%" . $filter . "%'";
        $sql .= " ORDER BY mp.created_at DESC";

        $result = $conn->query($sql);
        $patients = [];
        while ($row = $result->fetch_assoc()) {
            $row['conditions'] = json_decode($row['conditions'], true) ?: explode(',', $row['conditions']);
            // Check if in clinic
            $check = $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE patient_id = {$row['id']} AND time_out IS NULL");
            $row['in_clinic'] = $check->fetch_assoc()['c'] > 0;
            $patients[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $patients]);
        break;

    case 'register_patient':
        $reg_type = sanitize($conn, $_POST['reg_type'] ?? 'existing');
        if ($reg_type === 'new') {
            $name = sanitize($conn, $_POST['new_name'] ?? '');
            $stuId = sanitize($conn, $_POST['new_id'] ?? '');
            $gender = sanitize($conn, $_POST['new_gender'] ?? '');
            $age = intval($_POST['new_age'] ?? 0);

            $stmt = $conn->prepare("INSERT INTO students (name, student_id, gender, age) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $stuId, $gender, $age);
            if ($stmt->execute()) {
                $student_id = $conn->insert_id;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create student: ' . $conn->error]);
                exit;
            }
            $stmt->close();
        } else {
            $student_id = intval($_POST['student_id'] ?? 0);
        }
        $conditions = $_POST['conditions'] ?? '[]';
        $severity = sanitize($conn, $_POST['severity'] ?? 'Standard');
        $notes = sanitize($conn, $_POST['notes'] ?? '');
        $reg_date = sanitize($conn, $_POST['registered_date'] ?? date('Y-m-d H:i:s'));
        $reg_by = sanitize($conn, $_POST['registered_by'] ?? '');

        $patient_id = 'MED-' . time();

        $stmt = $conn->prepare("INSERT INTO medical_patients (patient_id, student_id, conditions, severity, notes, registered_date, registered_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssss", $patient_id, $student_id, $conditions, $severity, $notes, $reg_date, $reg_by);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Patient registered']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to register: ' . $conn->error]);
        }
        $stmt->close();
        break;

    case 'delete_patient':
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM medical_patients WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute(), 'message' => $stmt->execute() ? 'Deleted' : 'Failed']);
        $stmt->close();
        break;

    case 'get_patient':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT mp.*, s.name as student_name, s.student_id as stu_id, s.gender, s.age, s.grade_level, s.strand 
                                FROM medical_patients mp 
                                LEFT JOIN students s ON mp.student_id = s.id 
                                WHERE mp.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
        if ($patient) {
            $patient['conditions'] = json_decode($patient['conditions'], true) ?: explode(',', $patient['conditions']);

            $visits = $conn->query("SELECT mv.*, o.name as officer_name FROM medical_visits mv LEFT JOIN officers o ON mv.officer_id = o.id WHERE mv.patient_id = {$id} ORDER BY mv.visit_date DESC LIMIT 5");
            $patient['visits'] = [];
            while ($v = $visits->fetch_assoc())
                $patient['visits'][] = $v;
        }
        echo json_encode(['success' => true, 'data' => $patient]);
        $stmt->close();
        break;


    case 'list_visits':
        $status = sanitize($conn, $_GET['status'] ?? 'all');
        $date_filter = sanitize($conn, $_GET['date_filter'] ?? 'all');

        $sql = "SELECT mv.*, mp.patient_id as med_patient_id, s.name as student_name, s.student_id as stu_id, o.name as officer_name 
                FROM medical_visits mv 
                LEFT JOIN medical_patients mp ON mv.patient_id = mp.id 
                LEFT JOIN students s ON mp.student_id = s.id 
                LEFT JOIN officers o ON mv.officer_id = o.id 
                WHERE 1=1";

        if ($status === 'active')
            $sql .= " AND mv.time_out IS NULL";
        if ($status === 'completed')
            $sql .= " AND mv.time_out IS NOT NULL";

        $today = date('Y-m-d');
        if ($date_filter === 'today')
            $sql .= " AND mv.visit_date = '$today'";
        elseif ($date_filter === 'week')
            $sql .= " AND mv.visit_date >= DATE_SUB('$today', INTERVAL 7 DAY)";
        elseif ($date_filter === 'month')
            $sql .= " AND MONTH(mv.visit_date) = MONTH('$today') AND YEAR(mv.visit_date) = YEAR('$today')";

        $sql .= " ORDER BY mv.visit_date DESC, mv.time_in DESC";

        $result = $conn->query($sql);
        $visits = [];
        while ($row = $result->fetch_assoc())
            $visits[] = $row;


        $stats = [
            'today' => $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE visit_date = '$today'")->fetch_assoc()['c'],
            'active' => $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE time_out IS NULL")->fetch_assoc()['c'],
            'week' => $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE visit_date >= DATE_SUB('$today', INTERVAL 7 DAY)")->fetch_assoc()['c'],
            'avg_duration' => $conn->query("SELECT IFNULL(ROUND(AVG(duration)), 0) as avg FROM medical_visits WHERE duration IS NOT NULL")->fetch_assoc()['avg']
        ];

        echo json_encode(['success' => true, 'data' => $visits, 'stats' => $stats]);
        break;

    case 'export_visits':
        $status = sanitize($conn, $_GET['status'] ?? 'all');
        $date_filter = sanitize($conn, $_GET['date_filter'] ?? 'all');

        $sql = "SELECT mv.*, mp.patient_id as med_patient_id, s.name as student_name, s.student_id as stu_id, o.name as officer_name 
                FROM medical_visits mv 
                LEFT JOIN medical_patients mp ON mv.patient_id = mp.id 
                LEFT JOIN students s ON mp.student_id = s.id 
                LEFT JOIN officers o ON mv.officer_id = o.id 
                WHERE 1=1";

        if ($status === 'active')
            $sql .= " AND mv.time_out IS NULL";
        if ($status === 'completed')
            $sql .= " AND mv.time_out IS NOT NULL";

        $today = date('Y-m-d');
        if ($date_filter === 'today')
            $sql .= " AND mv.visit_date = '$today'";
        elseif ($date_filter === 'week')
            $sql .= " AND mv.visit_date >= DATE_SUB('$today', INTERVAL 7 DAY)";
        elseif ($date_filter === 'month')
            $sql .= " AND MONTH(mv.visit_date) = MONTH('$today') AND YEAR(mv.visit_date) = YEAR('$today')";

        $sql .= " ORDER BY mv.visit_date DESC, mv.time_in DESC";

        $result = $conn->query($sql);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Clinic_Visits_' . date('Y-m-d') . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Student Name', 'Student ID', 'Date', 'Time In', 'Time Out', 'Duration (mins)', 'Officer', 'Status', 'Notes', 'Treatment']);

        while ($row = $result->fetch_assoc()) {
            $status_text = !$row['time_out'] ? 'Active' : 'Completed';
            $duration_text = $row['duration'] ? $row['duration'] : ($row['time_out'] ? 'N/A' : '-');
            fputcsv($output, [
                $row['student_name'] ?: 'Unknown',
                $row['stu_id'] ?: '',
                $row['visit_date'],
                $row['time_in'],
                $row['time_out'] ?: '-',
                $duration_text,
                $row['officer_name'] ?: '-',
                $status_text,
                $row['notes'],
                $row['treatment']
            ]);
        }
        fclose($output);
        exit;

    case 'add_visit':
        $patient_id = intval($_POST['patient_id']);
        $date = sanitize($conn, $_POST['visit_date']);
        $time_in = sanitize($conn, $_POST['time_in']);
        $time_out = !empty($_POST['time_out']) ? sanitize($conn, $_POST['time_out']) : null;
        $officer_id = intval($_POST['officer_id']);
        $notes = sanitize($conn, $_POST['notes'] ?? '');
        $treatment = sanitize($conn, $_POST['treatment'] ?? '');

        $duration = null;
        if ($time_out) {
            $start = strtotime("2000-01-01 $time_in");
            $end = strtotime("2000-01-01 $time_out");
            $duration = round(($end - $start) / 60);
        }

        $visit_id = 'VIS-' . time();
        $stmt = $conn->prepare("INSERT INTO medical_visits (visit_id, patient_id, visit_date, time_in, time_out, duration, officer_id, notes, treatment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissssiss", $visit_id, $patient_id, $date, $time_in, $time_out, $duration, $officer_id, $notes, $treatment);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Visit logged']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed: ' . $conn->error]);
        }
        $stmt->close();
        break;

    case 'checkout_visit':
        $id = intval($_POST['id']);
        $time_out = date('H:i:s');

        $visit = $conn->query("SELECT time_in FROM medical_visits WHERE id = $id")->fetch_assoc();
        $start = strtotime("2000-01-01 " . $visit['time_in']);
        $end = strtotime("2000-01-01 $time_out");
        $duration = round(($end - $start) / 60);

        $stmt = $conn->prepare("UPDATE medical_visits SET time_out = ?, duration = ? WHERE id = ?");
        $stmt->bind_param("sii", $time_out, $duration, $id);
        echo json_encode(['success' => $stmt->execute(), 'message' => 'Checked out']);
        $stmt->close();
        break;

    case 'delete_visit':
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM medical_visits WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute()]);
        $stmt->close();
        break;


    case 'list_officers':
        $result = $conn->query("SELECT * FROM officers ORDER BY name");
        $officers = [];
        while ($row = $result->fetch_assoc())
            $officers[] = $row;
        echo json_encode(['success' => true, 'data' => $officers]);
        break;

    case 'add_officer':
        $name = sanitize($conn, $_POST['name']);
        $role = sanitize($conn, $_POST['role']);
        $license = sanitize($conn, $_POST['license'] ?? '');
        $contact = sanitize($conn, $_POST['contact'] ?? '');
        $officer_id = 'OFF-' . time();

        $stmt = $conn->prepare("INSERT INTO officers (officer_id, name, role, license_number, contact) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $officer_id, $name, $role, $license, $contact);
        $success = $stmt->execute();
        echo json_encode(['success' => $success, 'message' => $success ? 'Registered' : 'Failed: ' . $stmt->error]);
        $stmt->close();
        break;

    case 'delete_officer':
        $id = intval($_POST['id']);
        $count = $conn->query("SELECT COUNT(*) as c FROM officers")->fetch_assoc()['c'];
        if ($count <= 1) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete the last officer']);
            break;
        }
        $stmt = $conn->prepare("DELETE FROM officers WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute()]);
        $stmt->close();
        break;


    case 'list_officer_logs':
        $result = $conn->query("SELECT ol.*, o.name as officer_name, o.role as officer_role FROM officer_logs ol LEFT JOIN officers o ON ol.officer_id = o.id ORDER BY ol.log_date DESC, ol.time_in DESC");
        $logs = [];
        while ($row = $result->fetch_assoc())
            $logs[] = $row;

        $today = date('Y-m-d');
        $stats = [
            'roster' => $conn->query("SELECT COUNT(*) as c FROM officers")->fetch_assoc()['c'],
            'hours_today' => $conn->query("SELECT IFNULL(ROUND(SUM(duration)/60, 1), 0) as h FROM officer_logs WHERE log_date = '$today'")->fetch_assoc()['h']
        ];

        echo json_encode(['success' => true, 'data' => $logs, 'stats' => $stats]);
        break;

    case 'add_officer_log':
        $officer_id = intval($_POST['officer_id']);
        $date = sanitize($conn, $_POST['log_date']);
        $time_in = sanitize($conn, $_POST['time_in']);
        $time_out = !empty($_POST['time_out']) ? sanitize($conn, $_POST['time_out']) : null;
        $notes = sanitize($conn, $_POST['notes'] ?? '');

        $duration = null;
        if ($time_out) {
            $start = strtotime("2000-01-01 $time_in");
            $end = strtotime("2000-01-01 $time_out");
            $duration = round(($end - $start) / 60);
        }

        $log_id = 'LOG-' . time() . '-' . rand(100, 999);
        $stmt = $conn->prepare("INSERT INTO officer_logs (log_id, officer_id, log_date, time_in, time_out, duration, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sisssis", $log_id, $officer_id, $date, $time_in, $time_out, $duration, $notes);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Duty logged successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to log duty: ' . $stmt->error]);
        }
        $stmt->close();
        break;


    case 'patient_stats':
        $today = date('Y-m-d');
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => $conn->query("SELECT COUNT(*) as c FROM medical_patients")->fetch_assoc()['c'],
                'critical' => $conn->query("SELECT COUNT(*) as c FROM medical_patients WHERE severity = 'Critical'")->fetch_assoc()['c'],
                'today_visits' => $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE visit_date = '$today'")->fetch_assoc()['c'],
                'active' => $conn->query("SELECT COUNT(*) as c FROM medical_visits WHERE time_out IS NULL")->fetch_assoc()['c']
            ]
        ]);
        break;


    case 'available_students':
        $result = $conn->query("SELECT s.* FROM students s WHERE s.id NOT IN (SELECT student_id FROM medical_patients) ORDER BY s.name");
        $students = [];
        while ($row = $result->fetch_assoc())
            $students[] = $row;
        echo json_encode(['success' => true, 'data' => $students]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>