<?php
/**
 * Login Controller
 * متحكم تسجيل الدخول
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

class LoginController
{
    private $conn;
    private $error = '';
    private $success = '';

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Handle login form submission
     * معالجة إرسال نموذج تسجيل الدخول
     */
    public function handleLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validation
            if (empty($email) || empty($password)) {
                throw new ValidationException(
                    'Missing required fields',
                    3001,
                    ['email' => 'البريد الإلكتروني مطلوب', 'password' => 'كلمة المرور مطلوبة'],
                    'يرجى ملء جميع الحقول'
                );
            }

            if (!validateEmail($email)) {
                throw new ValidationException(
                    'Invalid email format',
                    3001,
                    ['email' => 'البريد الإلكتروني غير صحيح'],
                    'البريد الإلكتروني غير صحيح'
                );
            }

            // Check if user exists
            $stmt = $this->conn->prepare("SELECT user_id AS id, name, email, password, role FROM users WHERE email = ?");
            
            if (!$stmt) {
                throw new DatabaseException(
                    "Prepare failed: " . $this->conn->error,
                    1001,
                    'فشل الاتصال بقاعدة البيانات'
                );
            }

            $stmt->bind_param("s", $email);
            
            if (!$stmt->execute()) {
                throw new DatabaseException(
                    "Execute failed: " . $stmt->error,
                    1001,
                    'فشل الاتصال بقاعدة البيانات'
                );
            }

            $result = $stmt->get_result();

            if (!$result) {
                throw new DatabaseException(
                    "Get result failed: " . $this->conn->error,
                    1001,
                    'فشل الاتصال بقاعدة البيانات'
                );
            }

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // Verify password
                if (verifyPassword($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];

                    // Log successful login
                    ErrorLogger::info('User logged in successfully', ['user_id' => $user['id'], 'email' => $email]);

                    // Redirect to dashboard
                    header("Location: " . SITE_URL . "/dashboard.php");
                    exit;
                } else {
                    // Log failed login attempt
                    ErrorLogger::warning('Failed login attempt - wrong password', ['email' => $email]);
                    throw new AuthException(
                        'Wrong password',
                        2001,
                        'كلمة المرور غير صحيحة'
                    );
                }
            } else {
                // Log failed login attempt
                ErrorLogger::warning('Failed login attempt - user not found', ['email' => $email]);
                throw new AuthException(
                    'User not found',
                    2001,
                    'البريد الإلكتروني غير موجود'
                );
            }
        } catch (ValidationException $e) {
            $this->error = $e->getUserMessage();
            ErrorLogger::warning('Validation error on login', $e->getErrors());
        } catch (AuthException $e) {
            $this->error = $e->getUserMessage();
            ErrorLogger::warning($e->getLogMessage());
        } catch (DatabaseException $e) {
            $this->error = $e->getUserMessage();
            ErrorLogger::error($e->getLogMessage());
        } catch (Exception $e) {
            $this->error = 'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً.';
            ErrorLogger::error('Unexpected error in login: ' . $e->getMessage());
        }
    }

    /**
     * Get error message
     * الحصول على رسالة الخطأ
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Get success message
     * الحصول على رسالة النجاح
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Check if there's an error
     * التحقق من وجود خطأ
     */
    public function hasError()
    {
        return !empty($this->error);
    }

    /**
     * Check if there's a success message
     * التحقق من وجود رسالة نجاح
     */
    public function hasSuccess()
    {
        return !empty($this->success);
    }
}
?>
