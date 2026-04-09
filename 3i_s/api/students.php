<?php
require_once __DIR__ . '/../db_config.php';
requireLogin();

header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $search = sanitize($conn, $_GET['search'] ?? '');
        $strand = sanitize($conn, $_GET['strand'] ?? '');
        $grade = sanitize($conn, $_GET['grade'] ?? '');
        $gender = sanitize($conn, $_GET['gender'] ?? '');

        $sql = "SELECT s.*, 
                (SELECT COUNT(*) FROM medical_patients mp WHERE mp.student_id = s.id) as has_medical
                FROM students s WHERE 1=1";

        if ($search)
            $sql .= " AND (s.name LIKE '%$search%' OR s.student_id LIKE '%$search%')";
        if ($strand)
            $sql .= " AND s.strand = '$strand'";
        if ($grade)
            $sql .= " AND s.grade_level = '$grade'";
        if ($gender)
            $sql .= " AND s.gender = '$gender'";

        $sql .= " ORDER BY s.created_at DESC";
        $result = $conn->query($sql);
        $students = [];
        while ($row = $result->fetch_assoc())
            $students[] = $row;
        echo json_encode(['success' => true, 'data' => $students]);
        break;

    case 'add':
        $name = sanitize($conn, $_POST['name']);
        $gender = sanitize($conn, $_POST['gender']);
        $age = intval($_POST['age']);
        $academic_year = sanitize($conn, $_POST['academic_year'] ?? '');
        $grade = sanitize($conn, $_POST['grade_level']);
        $strand = sanitize($conn, $_POST['strand']);
        $guardian = sanitize($conn, $_POST['guardian_name'] ?? '');
        $address = sanitize($conn, $_POST['address'] ?? '');
        $emergency = sanitize($conn, $_POST['emergency_contact'] ?? '');
        $relationship = sanitize($conn, $_POST['relationship'] ?? '');


        $year = date('Y');
        $count = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'] + 1;
        $student_id = "STU-$year-" . str_pad($count, 3, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare("INSERT INTO students (student_id, name, gender, age, academic_year, grade_level, strand, guardian_name, address, emergency_contact, relationship, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $uid = $_SESSION['user_id'];
        $stmt->bind_param("sssississssi", $student_id, $name, $gender, $age, $academic_year, $grade, $strand, $guardian, $address, $emergency, $relationship, $uid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Student added successfully', 'student_id' => $student_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add student']);
        }
        $stmt->close();
        break;

    case 'edit':
        $id = intval($_POST['id']);
        $name = sanitize($conn, $_POST['name']);
        $gender = sanitize($conn, $_POST['gender']);
        $age = intval($_POST['age']);
        $academic_year = sanitize($conn, $_POST['academic_year'] ?? '');
        $grade = sanitize($conn, $_POST['grade_level']);
        $strand = sanitize($conn, $_POST['strand']);
        $guardian = sanitize($conn, $_POST['guardian_name'] ?? '');
        $address = sanitize($conn, $_POST['address'] ?? '');
        $emergency = sanitize($conn, $_POST['emergency_contact'] ?? '');
        $relationship = sanitize($conn, $_POST['relationship'] ?? '');

        $stmt = $conn->prepare("UPDATE students SET name=?, gender=?, age=?, academic_year=?, grade_level=?, strand=?, guardian_name=?, address=?, emergency_contact=?, relationship=? WHERE id=?");
        $stmt->bind_param("ssisssssssi", $name, $gender, $age, $academic_year, $grade, $strand, $guardian, $address, $emergency, $relationship, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update student']);
        }
        $stmt->close();
        break;

    case 'delete':
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete student']);
        }
        $stmt->close();
        break;

    case 'get':
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $student = $stmt->get_result()->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $student]);
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>