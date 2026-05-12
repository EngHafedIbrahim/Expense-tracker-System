<?php
/**
 * Footer Component
 * مكون التذييل
 */
?>
</main>
    </div>
</div>

<footer class="footer">
    <div class="container footer-top">
        <div>
            <div class="footer-brand">
                <div class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2.5 3 7v10l9 4.5 9-4.5V7l-9-4.5zm0 2.24L18.82 8 12 11.27 5.18 8 12 4.74zM5 9.62l6 3v7.86l-6-3V9.62zm14 0v7.86l-6 3v-7.86l6-3z"/>
                    </svg>
                </div>
                <div>
                    <h4>Expense Tracker</h4>
                    <p>واجهة حديثة لإدارة المصروفات والميزانيات والتقارير بكل سهولة.</p>
                </div>
            </div>
        </div>
        <div>
            <h4>روابط سريعة</h4>
            <div class="footer-links">
                <a href="<?php echo SITE_URL; ?>/dashboard.php">لوحة التحكم</a>
                <a href="<?php echo SITE_URL; ?>/categories.php">التصنيفات</a>
                <a href="<?php echo SITE_URL; ?>/expenses.php">المصروفات</a>
                <a href="<?php echo SITE_URL; ?>/reports.php">التقارير</a>
            </div>
        </div>
        <div>
            <h4>نبذة</h4>
            <p>
                يساعدك هذا النظام على متابعة نفقاتك اليومية وعرض ملخصات دقيقة، واتخاذ قرارات مالية أفضل من خلال بيانات منظمة وتقارير واضحة.
            </p>
        </div>
    </div>

    <div class="container footer-bottom">
        <div>© <span data-year></span> Expense Tracker. جميع الحقوق محفوظة.</div>
        <div>تصميم أنيق، متجاوب، ومعاد للغة العربية.</div>
    </div>
</footer>

</body>

<!-- JavaScript -->
<script src="<?php echo SITE_URL; ?>/assets/js/script.js"></script>
</html>
