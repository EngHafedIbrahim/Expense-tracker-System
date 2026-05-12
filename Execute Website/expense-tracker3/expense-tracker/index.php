<?php
/**
 * Home Page
 * الصفحة الرئيسية
 */

require_once 'includes/app.php';
initPage('الصفحة الرئيسية', false);

// If logged in, redirect to dashboard
if (isLoggedIn()) {
    header("Location: " . SITE_URL . "/dashboard.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-card">
            <div class="hero-grid">
                <div>
                    <h2>مرحباً بك في Expense Tracker</h2>
                    <p>نظام إدارة مصروفات حديث وسهل الاستخدام يساعدك على تتبع نفقاتك اليومية وإدارة ميزانيتك بكفاءة. احصل على تقارير مفصلة وتحليلات شاملة لإنفاقك.</p>
                    <div class="hero-badges">
                        <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary">إنشاء حساب مجاني</a>
                        <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-secondary">تسجيل الدخول</a>
                    </div>
                </div>
                <div class="hero-panel">
                    <div class="mini-card">
                        <div>
                            <strong>100%</strong>
                            <span>آمن وموثوق</span>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div>
                            <strong>سهل</strong>
                            <span>واجهة بسيطة</span>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div>
                            <strong>سريع</strong>
                            <span>أداء عالي</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section">
        <div class="section-head">
            <h3>المميزات الرئيسية</h3>
            <p>كل ما تحتاجه لإدارة مصروفاتك بكفاءة</p>
        </div>

        <div class="grid-3">
            <div class="card">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">💰</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem;">إدارة المصروفات</h4>
                <p style="color: var(--muted); font-size: 0.92rem;">أضف وعدّل وحذف مصروفاتك بسهولة. تتبع كل نفقة مع تاريخ وتصنيف وملاحظات.</p>
            </div>

            <div class="card">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">📂</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem;">تصنيفات مخصصة</h4>
                <p style="color: var(--muted); font-size: 0.92rem;">أنشئ تصنيفات خاصة بك مع ألوان وأيقونات مخصصة لتنظيم مصروفاتك.</p>
            </div>

            <div class="card">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">💵</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem;">ميزانية شهرية</h4>
                <p style="color: var(--muted); font-size: 0.92rem;">حدد ميزانية شهرية وتابع إنفاقك مقابلها. احصل على تنبيهات عند الاقتراب من الحد.</p>
            </div>

            <div class="card">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">📊</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem;">تقارير وإحصائيات</h4>
                <p style="color: var(--muted); font-size: 0.92rem;">اعرض تقارير مفصلة وإحصائيات شاملة عن إنفاقك وأنماط استهلاكك.</p>
            </div>

            <div class="card">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">📱</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem;">متجاوب وسهل</h4>
                <p style="color: var(--muted); font-size: 0.92rem;">واجهة متجاوبة تعمل على جميع الأجهزة - الهاتف والجهاز اللوحي والكمبيوتر.</p>
            </div>

            <div class="card">
                <div style="font-size: 2.5rem; margin-bottom: 12px;">🌍</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem;">دعم اللغة العربية</h4>
                <p style="color: var(--muted); font-size: 0.92rem;">واجهة كاملة باللغة العربية مع دعم RTL للكتابة من اليمين لليسار.</p>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section">
        <div class="section-head">
            <h3>كيفية الاستخدام</h3>
            <p>ثلاث خطوات بسيطة للبدء</p>
        </div>

        <div class="grid-3">
            <div class="card">
                <div style="font-size: 3rem; margin-bottom: 12px; text-align: center;">1️⃣</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem; text-align: center;">إنشاء حساب</h4>
                <p style="color: var(--muted); font-size: 0.92rem; text-align: center;">أنشئ حسابك مجاناً بملء بيانات بسيطة والتحقق من بريدك الإلكتروني.</p>
            </div>

            <div class="card">
                <div style="font-size: 3rem; margin-bottom: 12px; text-align: center;">2️⃣</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem; text-align: center;">أضف مصروفاتك</h4>
                <p style="color: var(--muted); font-size: 0.92rem; text-align: center;">ابدأ بإضافة مصروفاتك اليومية مع تحديد التصنيف والمبلغ والتاريخ.</p>
            </div>

            <div class="card">
                <div style="font-size: 3rem; margin-bottom: 12px; text-align: center;">3️⃣</div>
                <h4 style="margin: 0 0 8px; font-size: 1.1rem; text-align: center;">تابع وحلل</h4>
                <p style="color: var(--muted); font-size: 0.92rem; text-align: center;">اعرض التقارير والإحصائيات وتابع إنفاقك مقابل ميزانيتك.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section">
        <div class="card" style="background: linear-gradient(135deg, rgba(37, 99, 235, 0.94), rgba(20, 184, 166, 0.92)); color: #fff; text-align: center; padding: 60px 40px;">
            <h3 style="margin: 0 0 16px; font-size: 2rem; color: #fff;">ابدأ الآن مجاناً</h3>
            <p style="margin: 0 0 24px; font-size: 1.1rem; color: rgba(255, 255, 255, 0.92); max-width: 600px; margin-left: auto; margin-right: auto;">
                انضم إلى آلاف المستخدمين الذين يثقون بـ Expense Tracker لإدارة مصروفاتهم بكفاءة وأمان.
            </p>
            <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary" style="background: #fff; color: var(--primary);">إنشاء حساب الآن</a>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
