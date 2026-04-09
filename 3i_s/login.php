<?php
require_once 'db_config.php';

if (isAdminLoggedIn() && isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$code_status = '';
$code_info = null;


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = sanitize($conn, $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, status, unique_code FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'approved') {

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_type'] = 'teacher';
                    header('Location: dashboard.php');
                    exit();
                } elseif ($user['status'] === 'pending') {
                    $error = 'Your account is pending approval. Your unique code: <strong>' . htmlspecialchars($user['unique_code']) . '</strong>';
                } else {
                    $error = 'Your account has been rejected. Contact admin for support.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
        } else {

            $astmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            $astmt->bind_param("s", $email);
            $astmt->execute();
            $ares = $astmt->get_result();
            if ($ares->num_rows === 1) {
                $admin = $ares->fetch_assoc();
                if (password_verify($password, $admin['password'])) {

                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['user_type'] = 'admin';
                    header('Location: admin/dashboard.php');
                    exit();
                }
            }
            $error = 'Invalid email or password.';
        }
        $stmt->close();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_code'])) {
    $code = sanitize($conn, $_POST['unique_code'] ?? '');
    if (!empty($code)) {
        $stmt = $conn->prepare("SELECT username, email, unique_code, status, created_at FROM users WHERE unique_code = ?");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $code_info = $result->fetch_assoc();
        } else {
            $code_status = 'Code not found. Please check and try again.';
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
    <title>FEPC Medical Student Record - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('https://i.imgur.com/KVcPt0Z.jpeg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px 35px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.6s ease-out;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-section img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 12px;
            border: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .logo-section h1 {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            line-height: 1.5;
        }

        .logo-section h1 span {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #a7f3d0;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 18px;
            background: rgba(255, 255, 255, 0.85);
            border: 2px solid transparent;
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            color: #333;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input::placeholder {
            color: #999;
            font-weight: 400;
        }

        .form-group input:focus {
            border-color: #34d399;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1rem;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #34d399;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #34d399, #059669);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #2dd4a8, #047857);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .links {
            text-align: center;
            margin-top: 20px;
        }

        .links a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.3s ease;
            display: inline-block;
        }

        .links a:hover {
            color: #a7f3d0;
        }

        .links .separator {
            display: block;
            height: 8px;
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-align: center;
            line-height: 1.5;
        }

        .error-msg strong {
            color: #fbbf24;
            font-family: 'Courier New', monospace;
            letter-spacing: 3px;
            font-size: 1rem;
        }

        .success-msg {
            background: rgba(52, 211, 153, 0.15);
            border: 1px solid rgba(52, 211, 153, 0.3);
            color: #a7f3d0;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 16px;
            text-align: center;
        }


        .tabs {
            display: flex;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            padding: 4px;
        }

        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.6);
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .tab-btn:hover:not(.active) {
            color: rgba(255, 255, 255, 0.8);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }


        .code-result {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            margin-top: 12px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .code-result .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: rgba(251, 191, 36, 0.2);
            color: #fbbf24;
        }

        .status-approved {
            background: rgba(52, 211, 153, 0.2);
            color: #34d399;
        }

        .status-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .code-result p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.8rem;
            margin-top: 8px;
        }

        .code-result .code-display {
            font-family: 'Courier New', monospace;
            font-size: 1.3rem;
            font-weight: 800;
            color: #a7f3d0;
            letter-spacing: 4px;
            margin: 8px 0;
        }

        .btn-check {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        .btn-check:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <img src="https://i.imgur.com/viB2Fee.jpeg" alt="FEPC Logo">
                <h1>FAR EASTERN POLYTECHNIC COLLEGE <span>MEDICAL STUDENT RECORD</span></h1>
            </div>

            <?php if (isset($_GET['registered'])): ?>
                <div class="success-msg"><i class="fas fa-check-circle"></i> Account created! Wait for admin approval before
                    logging in.</div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('login')"><i class="fas fa-sign-in-alt"></i> Log
                    In</button>
                <button class="tab-btn" onclick="switchTab('code')"><i class="fas fa-key"></i> Check Code</button>
            </div>


            <div id="tab-login" class="tab-panel active">
                <?php if ($error): ?>
                    <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="login" value="1">
                    <div class="form-group">
                        <input type="email" name="email" placeholder="EMAIL"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group password-wrapper">
                        <input type="password" name="password" id="password" placeholder="PASSWORD" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <button type="submit" class="btn-login">LOG IN</button>
                </form>

                <div class="links">
                    <a href="signup.php">Don't have an account? <strong>Sign Up</strong></a>
                    <span class="separator"></span>
                    <a href="forgot_password.php"><i class="fas fa-lock"></i> Forgot Password?</a>
                </div>
            </div>


            <div id="tab-code" class="tab-panel">
                <form method="POST" action="">
                    <input type="hidden" name="check_code" value="1">
                    <div class="form-group">
                        <input type="text" name="unique_code" placeholder="ENTER YOUR UNIQUE CODE"
                            style="text-transform: uppercase; letter-spacing: 3px; text-align: center; font-weight: 700;"
                            value="<?php echo htmlspecialchars($_POST['unique_code'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn-check"><i class="fas fa-search"></i> Check Status</button>
                </form>

                <?php if ($code_status): ?>
                    <div class="error-msg" style="margin-top: 12px;"><i class="fas fa-times-circle"></i>
                        <?php echo $code_status; ?></div>
                <?php endif; ?>

                <?php if ($code_info): ?>
                    <div class="code-result">
                        <div class="code-display"><?php echo htmlspecialchars($code_info['unique_code']); ?></div>
                        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($code_info['username']); ?>
                            (<?php echo htmlspecialchars($code_info['email']); ?>)</p>
                        <p><i class="fas fa-calendar"></i> Registered:
                            <?php echo date('M d, Y', strtotime($code_info['created_at'])); ?>
                        </p>
                        <div style="margin-top: 10px;">
                            <?php if ($code_info['status'] === 'pending'): ?>
                                <span class="status-badge status-pending"><i class="fas fa-clock"></i> Pending Approval</span>
                                <p style="margin-top: 8px; color: #fbbf24; font-size: 0.75rem;"><i
                                        class="fas fa-info-circle"></i> Your account is waiting for admin approval. Please be
                                    patient.</p>
                            <?php elseif ($code_info['status'] === 'approved'): ?>
                                <span class="status-badge status-approved"><i class="fas fa-check-circle"></i> Approved</span>
                                <p style="margin-top: 8px; color: #34d399; font-size: 0.75rem;"><i class="fas fa-check"></i> You
                                    can now log in with your credentials!</p>
                            <?php else: ?>
                                <span class="status-badge status-rejected"><i class="fas fa-times-circle"></i> Rejected</span>
                                <p style="margin-top: 8px; color: #f87171; font-size: 0.75rem;"><i
                                        class="fas fa-exclamation-triangle"></i> Contact admin for assistance.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="links" style="margin-top: 15px;">
                    <a href="signup.php">Don't have a code? <strong>Sign Up</strong></a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            event.target.classList.add('active');
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        <?php if ($code_info || $code_status): ?>

            document.querySelectorAll('.tab-btn')[1].click();
        <?php endif; ?>
    </script>
</body>

</html>