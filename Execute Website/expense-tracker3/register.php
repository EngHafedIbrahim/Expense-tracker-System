<?php
/**
 * Register Page
 * صفحة التسجيل
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'controllers/RegisterController.php';

// If already logged in, redirect to dashboard
requireLogout();

// Initialize controller
$controller = new RegisterController($conn);

// Handle registration form submission
$controller->handleRegister();

// Load and render view
ob_start();
include 'views/register.html';
$content = ob_get_clean();

// Output content
echo $content;
?>
