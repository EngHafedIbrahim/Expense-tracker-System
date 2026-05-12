<?php
/**
 * Header Component
 * مكون الرأس
 */

require_once 'auth.php';

$currentPage = getCurrentPage();
$loggedIn = isLoggedIn();
$userName = getUserName();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2563eb">
    <meta name="description" content="نظام إدارة المصروفات - Expense Tracker">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Expense Tracker | تافورصملا ةرادإ</title>
    
    <!-- Google Fonts - Cairo for Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container topbar">
        <!-- Brand -->
        <div class="brand">
            <a href="<?php echo SITE_URL; ?>/dashboard.php" class="brand-mark" aria-label="العودة للرئيسية">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2.5 3 7v10l9 4.5 9-4.5V7l-9-4.5zm0 2.24L18.82 8 12 11.27 5.18 8 12 4.74zM5 9.62l6 3v7.86l-6-3V9.62zm14 0v7.86l-6 3v-7.86l6-3z"/>
                </svg>
            </a>
            <div class="brand-text">
                <h1>Expense Tracker</h1>
                <p>تافورصملا ةرادإ</p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="nav-wrap">
            <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-label="فتح القائمة">
                <span></span>
            </button>
            <?php if ($loggedIn): ?>
            <nav class="main-nav" data-nav-menu>
                <a href="<?php echo SITE_URL; ?>/dashboard.php" class="<?php echo isActive('dashboard.php'); ?>">لوحة التحكم</a>
                <a href="<?php echo SITE_URL; ?>/expenses.php" class="<?php echo isActive('expenses.php'); ?>">المصروفات</a>
                <a href="<?php echo SITE_URL; ?>/categories.php" class="<?php echo isActive('categories.php'); ?>">التصنيفات</a>
                <a href="<?php echo SITE_URL; ?>/budget.php" class="<?php echo isActive('budget.php'); ?>">الميزانية</a>
                <a href="<?php echo SITE_URL; ?>/reports.php" class="<?php echo isActive('reports.php'); ?>">التقارير</a>
            </nav>
            <?php endif; ?>
        </div>

        <!-- Header Actions -->
        <div class="header-actions">
            <?php if ($loggedIn): ?>
                <span class="badge" style="background:rgba(37,99,235,.08); color:#1d4ed8;">
                    أهلاً، <?php echo htmlspecialchars($userName); ?>
                </span>
                <a href="<?php echo SITE_URL; ?>/profile.php" class="btn btn-secondary">الملف الشخصي</a>
                <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-danger" onclick="return confirm('هل تريد تسجيل الخروج؟')">تسجيل الخروج</a>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary">تسجيل الدخول</a>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-secondary">إنشاء حساب</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
