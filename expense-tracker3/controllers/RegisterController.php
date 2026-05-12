<?php
/**
 * Register Controller
 * متحكم التسجيل
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

class RegisterController
{
    private $conn;
    private $error = '';
    private $success = '';

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    /**
     * Handle registration form submission
     * معالجة إرسال نموذج التسجيل
     */
    public function handleRegister()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        try {
            $name = sanitize($_POST['name'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Validation
            if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
                throw new ValidationException(
                    'Missing required fields',
                    3001,
                    [
                        'name' => empty($name) ? 'الاسم مطلوب' : '',
                        'email' => empty($email) ? 'البريد الإلكتروني مطلوب' : '',
                        'password' => empty($password) ? 'كلمة المرور مطلوبة' : '',
                        'password_confirm' => empty($password_confirm) ? 'تأكيد كلمة المرور مطلوب' : ''
                    ],
                    'يرجى ملء جميع الحقول'
                );
            }

            if (strlen($name) < 3) {
                throw new ValidationException(
                    'Name too short',
                    3001,
                    ['name' => 'الاسم يجب أن يكون 3 أحرف على الأقل'],
                    'الاسم يجب أن يكون 3 أحرف على الأقل'
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

            if (strlen($password) < 6) {
                throw new ValidationException(
                    'Password too short',
                    3001,
                    ['password' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل'],
                    'كلمة المرور يجب أن تكون 6 أحرف على الأقل'
                );
            }

            if ($password !== $password_confirm) {
                throw new ValidationException(
                    'Passwords do not match',
                    3001,
                    ['password_confirm' => 'كلمات المرور غير متطابقة'],
                    'كلمات المرور غير متطابقة'
                );
            }

            // Check if email already exists
            $stmt = $this->conn->prepare("SELECT user_id AS id FROM users WHERE email = ?");
            
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

            if ($result->num_rows > 0) {
                throw new ValidationException(
                    'Email already exists',
                    3001,
                    ['email' => 'البريد الإلكتروني مسجل بالفعل'],
                    'البريد الإلكتروني مسجل بالفعل'
                );
            }

            // Hash password
            $hashed_password = hashPassword($password);

            // Insert new user
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            
            if (!$stmt) {
                throw new DatabaseException(
                    "Prepare failed: " . $this->conn->error,
                    1001,
                    'فشل إنشاء الحساب'
                );
            }

            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if (!$stmt->execute()) {
                throw new DatabaseException(
                    "Execute failed: " . $stmt->error,
                    1001,
                    'فشل إنشاء الحساب'
                );
            }

            $this->success = 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول';
            
            // Log successful registration
            ErrorLogger::info('New user registered', ['email' => $email, 'name' => $name]);
            
            // Clear form
            $_POST = [];
        } catch (ValidationException $e) {
            $this->error = $e->getUserMessage();
            ErrorLogger::warning('Validation error on registration', $e->getErrors());
        } catch (DatabaseException $e) {
            $this->error = $e->getUserMessage();
            ErrorLogger::error($e->getLogMessage());
        } catch (Exception $e) {
            $this->error = 'حدث خطأ غير متوقع. يرجى المحاولة لاحقاً.';
            ErrorLogger::error('Unexpected error in registration: ' . $e->getMessage());
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
