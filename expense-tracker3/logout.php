<?php
/**
 * Logout Page
 * صفحة تسجيل الخروج
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

// Require login first
requireLogin();

// Destroy session
session_destroy();

// Redirect to login page
header("Location: " . SITE_URL . "/login.php?logout=1");
exit;
?>
