<?php
require_once 'db_config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';
$step = 'verify';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify'])) {
        $email = sanitize($conn, $_POST['email'] ?? '');
        $unique_code = sanitize($conn, $_POST['unique_code'] ?? '');

        if (empty($email) || empty($unique_code)) {
            $error = 'Please fill in all fields.';
        } else {
            $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? AND unique_code = ?");
            $stmt->bind_param("ss", $email, $unique_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['reset_username'] = $user['username'];
                $step = 'reset';
            } else {
                $error = 'Email and unique code do not match. Please check your information.';
            }
            $stmt->close();
        }
    }

    if (isset($_POST['reset_password'])) {
        $new_password = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($new_password) || empty($confirm)) {
            $error = 'Please fill in all fields.';
            $step = 'reset';
        } elseif ($new_password !== $confirm) {
            $error = 'Passwords do not match.';
            $step = 'reset';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters.';
            $step = 'reset';
        } elseif (!isset($_SESSION['reset_user_id'])) {
            $error = 'Session expired. Please start over.';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $uid = $_SESSION['reset_user_id'];
            $stmt->bind_param("si", $hashed, $uid);
            if ($stmt->execute()) {
                unset($_SESSION['reset_user_id']);
                unset($_SESSION['reset_username']);
                header('Location: login.php?reset=1');
                exit();
            } else {
                $error = 'Failed to reset password. Try again.';
                $step = 'reset';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FEPC Medical Student Record</title>
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

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .card {
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
            margin-bottom: 25px;
        }

        .logo-section img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid rgba(255, 255, 255, 0.4);
        }

        .logo-section h1 {
            color: #fff;
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

        .title {
            text-align: center;
            margin-bottom: 20px;
        }

        .title h2 {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .title p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            line-height: 1.5;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: #34d399;
            width: 24px;
            border-radius: 4px;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 18px;
            background: rgba(255, 255, 255, 0.85);
            border: 2px solid transparent;
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #333;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input::placeholder {
            color: #999;
        }

        .form-group input:focus {
            border-color: #34d399;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2);
        }

        .form-group input.code-input {
            text-transform: uppercase;
            letter-spacing: 4px;
            text-align: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #34d399, #059669);
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
            margin-top: 8px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #2dd4a8, #047857);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.4);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 14px;
            text-align: center;
        }

        .info-box {
            background: rgba(99, 102, 241, 0.15);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 18px;
            color: #a5b4fc;
            font-size: 0.8rem;
            line-height: 1.5;
            text-align: center;
        }

        .reset-user {
            text-align: center;
            color: #a7f3d0;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(52, 211, 153, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(52, 211, 153, 0.2);
        }

        .reset-user i {
            margin-right: 6px;
        }

        .links {
            text-align: center;
            margin-top: 18px;
        }

        .links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 0.85rem;
        }

        .links a:hover {
            color: #a7f3d0;
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
    <div class="container">
        <div class="card">
            <div class="logo-section">
                <img src="https://i.imgur.com/viB2Fee.jpeg" alt="FEPC Logo">
                <h1>FAR EASTERN POLYTECHNIC COLLEGE <span>MEDICAL STUDENT RECORD</span></h1>
            </div>

            <div class="step-indicator">
                <div class="step-dot <?php echo $step === 'verify' ? 'active' : ''; ?>"></div>
                <div class="step-dot <?php echo $step === 'reset' ? 'active' : ''; ?>"></div>
            </div>

            <?php if ($error): ?>
                <div class="error-msg"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($step === 'verify'): ?>
                <div class="title">
                    <h2><i class="fas fa-key"></i> Forgot Password</h2>
                    <p>Enter your email and unique code to verify your identity.</p>
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i> Your unique code was given to you when you signed up. If you lost it,
                    contact the admin.
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="verify" value="1">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="your@email.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Unique Code</label>
                        <input type="text" name="unique_code" placeholder="XXXXXXXX" class="code-input"
                            value="<?php echo htmlspecialchars($_POST['unique_code'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-arrow-right"></i> Verify Identity</button>
                </form>
            <?php else: ?>
                <div class="title">
                    <h2><i class="fas fa-lock-open"></i> Reset Password</h2>
                    <p>Create a new password for your account.</p>
                </div>

                <div class="reset-user">
                    <i class="fas fa-user-check"></i> Verified as:
                    <strong><?php echo htmlspecialchars($_SESSION['reset_username'] ?? 'User'); ?></strong>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="reset_password" value="1">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="Min. 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Re-type password" required>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Reset Password</button>
                </form>
            <?php endif; ?>

            <div class="links">
                <a href="login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>
</body>

</html>