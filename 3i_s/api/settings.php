<?php
require_once __DIR__ . '/../db_config.php';
requireLogin();

header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_profile':
        $name = sanitize($conn, $_POST['name']);
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
        $uid = $_SESSION['user_id'];
        $stmt->bind_param("si", $name, $uid);
        if ($stmt->execute()) {
            $_SESSION['username'] = $name;
            echo json_encode(['success' => true, 'message' => 'Profile updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        $stmt->close();
        break;

    case 'change_password':
        $current = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if ($new_password !== $confirm) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
            break;
        }
        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
            break;
        }
        
        $uid = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!password_verify($current, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            break;
        }
        
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $uid);
        echo json_encode(['success' => $update->execute(), 'message' => 'Password changed successfully']);
        $update->close();
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
