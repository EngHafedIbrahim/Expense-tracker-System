<?php
/**
 * Register Page
 * صفحة التسجيل
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

// If already logged in, redirect to dashboard
requireLogout();

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'يرجى ملء جميع الحقول';
    } elseif (!validateEmail($email)) {
        $error = 'البريد الإلكتروني غير صحيح';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    } elseif ($password !== $password_confirm) {
        $error = 'كلمات المرور غير متطابقة';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = 'البريد الإلكتروني مسجل بالفعل';
        } else {
            // Hash password
            $hashed_password = hashPassword($password);

            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول';
                // Clear form
                $_POST = [];
            } else {
                $error = 'حدث خطأ أثناء إنشاء الحساب';
            }
        }
    }
}

$pageTitle = 'إنشاء حساب';
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

        .register-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .register-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 40px;
        }

        .register-card h1 {
            margin: 0 0 8px;
            font-size: 1.8rem;
            text-align: center;
        }

        .register-card p {
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

        .btn-register {
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

        .btn-register:hover {
            transform: translateY(-1px);
            filter: saturate(1.05);
        }

        .register-footer {
            text-align: center;
            margin-top: 24px;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .register-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 800;
        }

        .register-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h1>إنشاء حساب</h1>
            <p>أنشئ حسابك الجديد للبدء</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <br><a href="<?php echo SITE_URL; ?>/login.php" style="color: inherit; text-decoration: underline;">اذهب لتسجيل الدخول</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">الاسم الكامل</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        placeholder="محمد أحمد"
                        value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="example@email.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required
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

                <div class="form-group">
                    <label for="password_confirm">تأكيد كلمة المرور</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn-register">إنشاء الحساب</button>
            </form>

            <div class="register-footer">
                هل لديك حساب بالفعل؟ <a href="<?php echo SITE_URL; ?>/login.php">تسجيل الدخول</a>
            </div>
        </div>
    </div>
</body>
</html>
