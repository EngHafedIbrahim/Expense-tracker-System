<?php
/**
 * Profile Page
 * صفحة الملف الشخصي
 */

require_once 'includes/app.php';

initPage('الملف الشخصي');
$user = getCurrentUser();

require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div class="section-head">
            <h3>الملف الشخصي</h3>
            <p>معاينة معلومات حسابك وتفاصيل المستخدم.</p>
        </div>

        <div class="card">
            <div class="profile-grid">
                <div class="profile-card">
                    <h4>معلومات المستخدم</h4>
                    <dl>
                        <dt>الاسم</dt>
                        <dd><?php echo htmlspecialchars($user['name'] ?? '---'); ?></dd>

                        <dt>البريد الإلكتروني</dt>
                        <dd><?php echo htmlspecialchars($user['email'] ?? '---'); ?></dd>

                        <dt>الدور</dt>
                        <dd><?php echo htmlspecialchars($user['role'] ?? 'المستخدم'); ?></dd>
                    </dl>
                </div>

                <div class="profile-card">
                    <h4>إعدادات الحساب</h4>
                    <p>يمكنك تحديث بياناتك أو تغيير كلمة المرور من هنا عندما تكون هذه الميزة متاحة.</p>
                    <div class="profile-actions">
                        <a href="<?php echo SITE_URL; ?>/dashboard.php" class="btn btn-secondary">العودة للوحة التحكم</a>
                        <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-danger" onclick="return confirm('هل تريد تسجيل الخروج؟')">تسجيل الخروج</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php';
