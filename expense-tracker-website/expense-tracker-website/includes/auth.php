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
    
    $userId = getCurrentUserId();
    $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
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
    session_destroy();
    header("Location: " . SITE_URL . "/login.php");
    exit;
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
