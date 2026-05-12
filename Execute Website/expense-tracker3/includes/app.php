<?php
/**
 * Application Bootstrap
 * ملف تهيئة التطبيق العام
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

/**
 * Initialize page settings
 * تهيئة إعدادات الصفحة
 *
 * @param string $title
 * @param bool $requireLogin
 */
function initPage(string $title = '', bool $requireLogin = true)
{
    global $pageTitle;

    if (!empty($title)) {
        $pageTitle = $title;
    }

    if ($requireLogin) {
        requireLogin();
    }
}
