<?php
require_once __DIR__ . '/../db_config.php';

if (isAdminLoggedIn() && isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
} elseif (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['user_type'] = 'admin';
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FEPC</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }
        .login-container { width: 100%; max-width: 420px; padding: 20px; }
        .login-card {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px 35px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.6s ease-out;
        }
        .logo-section { text-align: center; margin-bottom: 30px; }
        .logo-section .icon-wrap {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
        .logo-section .icon-wrap i { color: white; font-size: 28px; }
        .logo-section h1 { color: #fff; font-size: 1.3rem; font-weight: 700; }
        .logo-section p { color: #94a3b8; font-size: 0.85rem; margin-top: 5px; }
        .form-group { margin-bottom: 16px; }
        .form-group input {
            width: 100%; padding: 13px 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 10px; font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #e2e8f0; transition: all 0.3s ease; outline: none;
        }
        .form-group input::placeholder { color: #64748b; }
        .form-group input:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); background: rgba(255, 255, 255, 0.1); }
        .btn-login {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white; border: none; border-radius: 10px;
            font-size: 1rem; font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer; text-transform: uppercase; letter-spacing: 2px;
            transition: all 0.3s ease; margin-top: 5px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4); }
        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5; padding: 12px 16px;
            border-radius: 10px; font-size: 0.85rem;
            margin-bottom: 15px; text-align: center;
        }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #64748b; text-decoration: none; font-size: 0.85rem; }
        .back-link a:hover { color: #a5b4fc; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="icon-wrap"><i class="fas fa-shield-alt"></i></div>
                <h1>Admin Panel</h1>
                <p>FEPC Student Record System</p>
            </div>
            <?php if ($error): ?>
                <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group"><input type="text" name="username" placeholder="ADMIN USERNAME" required></div>
                <div class="form-group"><input type="password" name="password" placeholder="PASSWORD" required></div>
                <button type="submit" class="btn-login">Login</button>
            </form>
            <div class="back-link"><a href="../login.php"><i class="fas fa-arrow-left"></i> Back to Teacher Login</a></div>
        </div>
    </div>
</body>
</html>
