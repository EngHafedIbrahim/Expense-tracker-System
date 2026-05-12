<?php
/**
 * Login Page
 * صفحة تسجيل الدخول
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

// If already logged in, redirect to dashboard
requireLogout();

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول';
    } elseif (!validateEmail($email)) {
        $error = 'البريد الإلكتروني غير صحيح';
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (verifyPassword($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect to dashboard
                header("Location: " . SITE_URL . "/dashboard.php");
                exit;
            } else {
                $error = 'كلمة المرور غير صحيحة';
            }
        } else {
            $error = 'البريد الإلكتروني غير موجود';
        }
    }
}

$pageTitle = 'تسجيل الدخول';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <title><?php echo $pageTitle; ?> - Expense Tracker</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 40px;
        }

        .login-card h1 {
            margin: 0 0 8px;
            font-size: 1.8rem;
            text-align: center;
        }

        .login-card p {
            text-align: center;
            color: var(--muted);
            margin: 0 0 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 800;
            color: #0f172a;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border-radius: 16px;
            border: 1px solid #dbe3ee;
            background: #fff;
            color: var(--text);
            font-family: inherit;
            font-size: 1rem;
            outline: none;
            transition: 0.2s ease;
        }

        .form-group input:focus {
            border-color: rgba(37, 99, 235, 0.55);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .btn-login {
            width: 100%;
            padding: 13px 18px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), #3b82f6);
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            filter: saturate(1.05);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 800;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h1>تسجيل الدخول</h1>
            <p>أدخل بيانات حسابك للمتابعة</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="example@email.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">دخول</button>
            </form>

            <div class="login-footer">
                ليس لديك حساب؟ <a href="<?php echo SITE_URL; ?>/register.php">إنشاء حساب جديد</a>
            </div>
        </div>
    </div>
</body>
</html>
