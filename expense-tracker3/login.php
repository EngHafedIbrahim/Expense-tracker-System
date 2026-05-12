<?php
/**
 * Login Page
 * صفحة تسجيل الدخول
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'controllers/LoginController.php';

// If already logged in, redirect to dashboard
requireLogout();

// Initialize controller
$controller = new LoginController($conn);

// Handle login form submission
$controller->handleLogin();

// Load and render view
ob_start();
include 'views/login.html';
$content = ob_get_clean();

// Output content
echo $content;
?>
