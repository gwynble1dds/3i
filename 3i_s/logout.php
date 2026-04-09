<?php
require_once 'db_config.php';

// Specifically unset only Teacher session variables
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['email']);

// Optional: Also clear role indicators
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'teacher') {
    unset($_SESSION['user_type']);
}

header('Location: login.php');
exit();
?>
