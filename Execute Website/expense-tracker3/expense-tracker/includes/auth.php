<?php
/**
 * Authentication Helper
 * مساعد المصادقة
 */

require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * التحقق من تسجيل دخول المستخدم
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * الحصول على معرف المستخدم الحالي
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 * الحصول على بيانات المستخدم الحالي
 */
function getCurrentUser() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $userId = getCurrentUserId();
        $stmt = $conn->prepare("SELECT user_id AS id, name, email, role FROM users WHERE user_id = ?");
        
        if (!$stmt) {
            throw new DatabaseException(
                "Prepare failed: " . $conn->error,
                1001,
                "حدث خطأ في استرجاع بيانات المستخدم."
            );
        }
        
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) {
            throw new DatabaseException(
                "Query failed: " . $conn->error,
                1001,
                "حدث خطأ في استرجاع بيانات المستخدم."
            );
        }
        
        return $result->fetch_assoc();
    } catch (DatabaseException $e) {
        ErrorLogger::error($e->getLogMessage());
        throw $e;
    } catch (Exception $e) {
        ErrorLogger::error("Unexpected error in getCurrentUser: " . $e->getMessage());
        throw new DatabaseException(
            $e->getMessage(),
            1001,
            "حدث خطأ في استرجاع بيانات المستخدم."
        );
    }
}

/**
 * Require login - redirect to login page if not logged in
 * يتطلب تسجيل الدخول - إعادة التوجيه إلى صفحة تسجيل الدخول إذا لم يكن مسجلاً
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "/login.php");
        exit;
    }
}

/**
 * Require logout - redirect to dashboard if already logged in
 * يتطلب تسجيل الخروج - إعادة التوجيه إلى لوحة التحكم إذا كان مسجلاً بالفعل
 */
function requireLogout() {
    if (isLoggedIn()) {
        header("Location: " . SITE_URL . "/dashboard.php");
        exit;
    }
}

/**
 * Get current page name for active navigation
 * الحصول على اسم الصفحة الحالية للملاحة النشطة
 */
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

/**
 * Check if page is active
 * التحقق من أن الصفحة نشطة
 */
function isActive($page) {
    return getCurrentPage() === $page ? 'active' : '';
}

/**
 * Sanitize input
 * تنظيف الإدخال
 */
function sanitize($input) {
    global $conn;
    return $conn->real_escape_string(htmlspecialchars(strip_tags($input)));
}

/**
 * Validate email
 * التحقق من البريد الإلكتروني
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 * تجزئة كلمة المرور
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 * التحقق من كلمة المرور
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Logout user
 * تسجيل خروج المستخدم
 */
function logout() {
    try {
        // Log the logout action
        if (isLoggedIn()) {
            ErrorLogger::info('User logged out', ['user_id' => getCurrentUserId()]);
        }
        
        session_destroy();
        header("Location: " . SITE_URL . "/login.php");
        exit;
    } catch (Exception $e) {
        ErrorLogger::error("Error during logout: " . $e->getMessage());
        session_destroy();
        header("Location: " . SITE_URL . "/login.php");
        exit;
    }
}

/**
 * Get user name for display
 * الحصول على اسم المستخدم للعرض
 */
function getUserName() {
    return $_SESSION['user_name'] ?? 'المستخدم';
}

/**
 * Get user role
 * الحصول على دور المستخدم
 */
function getUserRole() {
    return $_SESSION['user_role'] ?? 'user';
}

/**
 * Check if user is admin
 * التحقق من أن المستخدم مسؤول
 */
function isAdmin() {
    return getUserRole() === 'admin';
}
?>
