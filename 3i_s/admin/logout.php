<?php
require_once __DIR__ . '/../db_config.php';

// Specifically unset only Admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

// Optional: Also clear any role indicators
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin') {
    unset($_SESSION['user_type']);
}

// Redirect back to Admin login
header('Location: login.php');
exit();
?>
