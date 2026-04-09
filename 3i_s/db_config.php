<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script = $_SERVER['SCRIPT_NAME'];
$project_folder = "3i's";
$root_url = $protocol . $host . "/" . $project_folder . "/";
define('ROOT_URL', $root_url);


$sessionPath = __DIR__ . DIRECTORY_SEPARATOR . 'sessions';
if (!is_dir($sessionPath))
    @mkdir($sessionPath, 0777, true);
ini_set('session.save_path', $sessionPath);



session_name('fepc_medical_session_v5');


ini_set('session.gc_maxlifetime', 31536000);
ini_set('session.cookie_lifetime', 31536000);
ini_set('session.gc_probability', 0);
ini_set('session.use_strict_mode', 1);


session_set_cookie_params([
    'lifetime' => 31536000,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$_SESSION['last_activity'] = time();

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'fepc_medical_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error)
    die("Connection failed: " . $conn->connect_error);
$conn->set_charset("utf8mb4");


// Auth Helpers
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function isAjax()
{
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ||
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        if (isAjax()) {
            session_write_close();
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
            exit();
        }
        header('Location: ' . ROOT_URL . 'login.php');
        exit();
    }
}

function requireAdminLogin()
{
    if (!isAdminLoggedIn()) {
        if (isAjax()) {
            session_write_close();
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Admin session expired.']);
            exit();
        }
        header('Location: ' . ROOT_URL . 'login.php');
        exit();
    }
}

function generateUniqueCode($length = 8)
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < $length; $i++)
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    return $code;
}

function sanitize($conn, $input)
{
    return $conn->real_escape_string(htmlspecialchars(trim($input)));
}
?>