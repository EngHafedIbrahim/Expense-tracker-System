<?php
/**
 * Database Configuration
 * تكوين قاعدة البيانات
 */

// Environment configuration
define('ENVIRONMENT', 'development'); // 'development' or 'production'
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Include exception handling
require_once __DIR__ . '/exceptions.php';

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'expense_tracker_db');

// Create connection with exception handling
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Set charset to utf8mb4 for Arabic support
    $conn->set_charset("utf8mb4");

    // Check connection
    if ($conn->connect_error) {
        throw new DatabaseException(
            "Connection failed: " . $conn->connect_error,
            1001,
            "فشل الاتصال بقاعدة البيانات. يرجى التحقق من إعدادات الاتصال."
        );
    }

    ErrorLogger::info('Database connection established successfully');
} catch (DatabaseException $e) {
    ErrorLogger::error($e->getLogMessage(), ['exception' => get_class($e)]);
    throw $e;
} catch (Exception $e) {
    ErrorLogger::error('Unexpected database error: ' . $e->getMessage());
    throw new DatabaseException(
        $e->getMessage(),
        1001,
        "حدث خطأ في الاتصال بقاعدة البيانات."
    );
}

// Site settings
define('SITE_NAME', 'Expense Tracker');

// Build a URL that includes the current host and port when available.
$siteHost = $_SERVER['HTTP_HOST'] ?? 'localhost:8';
$sitePath = '/expense-tracker3';
define('SITE_URL', 'http://' . $siteHost . $sitePath);

// Session configuration
ini_set('session.gc_maxlifetime', 86400); // 24 hours
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
