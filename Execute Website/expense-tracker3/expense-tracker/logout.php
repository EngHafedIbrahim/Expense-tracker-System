<?php
/**
 * Logout Page
 * صفحة تسجيل الخروج
 */

require_once 'includes/app.php';

// Require login first
initPage('', true);

// Destroy session
session_destroy();

// Redirect to login page
header("Location: " . SITE_URL . "/login.php?logout=1");
exit;
?>
