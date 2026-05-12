<?php
/**
 * Reports Page
 * صفحة التقارير
 */

require_once 'includes/app.php';

initPage('التقارير');
$userId = getCurrentUserId();
$errors = [];
$currentMonth = date('m');
$currentYear = date('Y');

$monthlyExpense = 0;
$totalExpense = 0;
$budgetAmount = 0;
$categorySummary = [];
$monthlySummary = [];

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE userId = ? AND MONTH(expenseDate) = ? AND YEAR(expenseDate) = ?");
$stmt->bind_param('iii', $userId, $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $monthlyExpense = $row['total'];
}
$stmt->close();

$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE userId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $totalExpense = $row['total'];
}
$stmt->close();

$stmt = $conn->prepare("SELECT budgetAmount FROM budgets WHERE userId = ? AND month = ? AND year = ?");
$stmt->bind_param('iii', $userId, $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $budgetAmount = $row['budgetAmount'];
}
$stmt->close();

$stmt = $conn->prepare("SELECT c.name, COALESCE(SUM(e.amount), 0) AS total FROM categories c LEFT JOIN expenses e ON c.id = e.categoryId AND e.userId = ? AND MONTH(e.expenseDate) = ? AND YEAR(e.expenseDate) = ? WHERE c.userId = ? GROUP BY c.id, c.name ORDER BY total DESC");
$stmt->bind_param('iiii', $userId, $currentMonth, $currentYear, $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categorySummary[] = $row;
}
$stmt->close();

$stmt = $conn->prepare("SELECT MONTH(expenseDate) AS month, YEAR(expenseDate) AS year, COALESCE(SUM(amount),0) AS total FROM expenses WHERE userId = ? GROUP BY YEAR(expenseDate), MONTH(expenseDate) ORDER BY year DESC, month DESC LIMIT 12");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $monthlySummary[] = $row;
}
$stmt->close();

$remainingBudget = $budgetAmount - $monthlyExpense;
require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div class="section-head">
            <h2>التقارير</h2>
            <p>ملخص الإنفاق والميزانية الخاص بك.</p>
        </div>

        <div class="grid-3 gap-24" style="margin-bottom: 32px;">
            <div class="card">
                <h3>إجمالي الإنفاق</h3>
                <p class="stat-number"><?php echo number_format($totalExpense, 2); ?> ر.س</p>
                <p>جميع المصروفات منذ البداية.</p>
            </div>
            <div class="card">
                <h3>إنفاق هذا الشهر</h3>
                <p class="stat-number"><?php echo number_format($monthlyExpense, 2); ?> ر.س</p>
                <p>حسب التاريخ الحالي.</p>
            </div>
            <div class="card">
                <h3>الميزانية المتبقية</h3>
                <p class="stat-number"><?php echo number_format($remainingBudget, 2); ?> ر.س</p>
                <p>الميزانية الشهرية ناقص الإنفاق الحالي.</p>
            </div>
        </div>

        <div class="card" style="margin-bottom: 32px;">
            <h3>تفصيل المصروفات حسب التصنيف (<?php echo date('F Y'); ?>)</h3>
            <?php if (!empty($categorySummary)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>التصنيف</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorySummary as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo number_format($row['total'], 2); ?> ر.س</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>لا توجد بيانات تصنيفات لهذا الشهر.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>ملخص الأشهر الأخيرة</h3>
            <?php if (!empty($monthlySummary)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>السنة</th>
                            <th>المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($monthlySummary as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(DateTime::createFromFormat('!m', $row['month'])->format('F')); ?></td>
                                <td><?php echo htmlspecialchars($row['year']); ?></td>
                                <td><?php echo number_format($row['total'], 2); ?> ر.س</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>لا توجد بيانات مصروفات حتى الآن.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php';

