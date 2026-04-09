<?php
require_once __DIR__ . '/../../db_config.php';

// Allow any logged-in user (Admin or Teacher) to access basic info
if (!isLoggedIn() && !isAdminLoggedIn()) {
    requireLogin(); // This will trigger the 401 JSON for AJAX
}

header('Content-Type: application/json');
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list_users':
        requireAdminLogin();
        $result = $conn->query("SELECT id, username, email, unique_code, status, created_at FROM users ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), created_at DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) $users[] = $row;
        echo json_encode(['success' => true, 'data' => $users]);
        break;

    case 'approve_user':
        requireAdminLogin();
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute(), 'message' => 'Account approved successfully']);
        $stmt->close();
        break;

    case 'reject_user':
        requireAdminLogin();
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $id);
        echo json_encode(['success' => $stmt->execute(), 'message' => 'Account rejected']);
        $stmt->close();
        break;

    case 'change_admin_password':
        $current = $_POST['current_password'];
        $new_pw = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if ($new_pw !== $confirm) { echo json_encode(['success' => false, 'message' => 'Passwords do not match']); break; }
        if (strlen($new_pw) < 6) { echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']); break; }
        
        $aid = $_SESSION['admin_id'];
        $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->bind_param("i", $aid);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        
        if (!password_verify($current, $admin['password'])) { echo json_encode(['success' => false, 'message' => 'Current password is incorrect']); break; }
        
        $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $aid);
        echo json_encode(['success' => $update->execute(), 'message' => 'Password changed successfully']);
        $update->close();
        $stmt->close();
        break;

    case 'check_new_users':
        $since = $_GET['since'] ?? date('Y-m-d H:i:s', strtotime('-30 seconds'));
        $stmt = $conn->prepare("SELECT id, username, email, created_at FROM users WHERE status = 'pending' AND created_at > ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $since);
        $stmt->execute();
        $result = $stmt->get_result();
        $new_users = [];
        while ($row = $result->fetch_assoc()) $new_users[] = $row;
        $pending_total = $conn->query("SELECT COUNT(*) as c FROM users WHERE status = 'pending'")->fetch_assoc()['c'];
        echo json_encode(['success' => true, 'new_users' => $new_users, 'pending_total' => intval($pending_total), 'server_time' => date('Y-m-d H:i:s')]);
        $stmt->close();
        break;

    case 'get_duty_officer':
        $today = date('Y-m-d');
        $stmt = $conn->prepare("SELECT ol.id as log_id, o.name, o.role, ol.time_in FROM officer_logs ol JOIN officers o ON ol.officer_id = o.id WHERE ol.log_date = ? AND ol.time_out IS NULL ORDER BY ol.time_in DESC");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $on_duty = [];
        while ($row = $result->fetch_assoc()) $on_duty[] = $row;
        echo json_encode(['success' => true, 'on_duty' => $on_duty]);
        $stmt->close();
        break;

    case 'get_all_duty_logs':
        $today = date('Y-m-d');
        $stmt = $conn->prepare("SELECT ol.id, o.name, o.role, ol.time_in, ol.time_out FROM officer_logs ol JOIN officers o ON ol.officer_id = o.id WHERE ol.log_date = ? ORDER BY ol.time_in DESC");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = [];
        while ($row = $result->fetch_assoc()) $logs[] = $row;
        echo json_encode(['success' => true, 'data' => $logs]);
        $stmt->close();
        break;

    case 'force_end_duty':
        requireAdminLogin();
        $log_id = intval($_POST['log_id']);
        $now = date('H:i:s');
        $stmt = $conn->prepare("UPDATE officer_logs SET time_out = ? WHERE id = ? AND time_out IS NULL");
        $stmt->bind_param("si", $now, $log_id);
        $success = $stmt->execute();
        echo json_encode(['success' => $success, 'message' => $success ? 'Officer duty ended manually' : 'Failed to end duty']);
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
