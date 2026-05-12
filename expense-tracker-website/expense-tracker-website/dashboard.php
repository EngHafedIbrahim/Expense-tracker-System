<?php
/**
 * Dashboard Page
 * صفحة لوحة التحكم
 */

require_once 'includes/header.php';

// Require login
requireLogin();

$userId = getCurrentUserId();

// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// Get total expenses for current month
$stmt = $conn->prepare("
    SELECT COALESCE(SUM(amount), 0) as total 
    FROM expenses 
    WHERE user_id = ? 
    AND MONTH(expense_date) = ? 
    AND YEAR(expense_date) = ?
");
$stmt->bind_param("iii", $userId, $currentMonth, $currentYear);
$stmt->execute();
$monthlyTotal = $stmt->get_result()->fetch_assoc()['total'];

// Get budget for current month
$stmt = $conn->prepare("
    SELECT budget_amount 
    FROM budgets 
    WHERE user_id = ? 
    AND month = ? 
    AND year = ?
");
$stmt->bind_param("iii", $userId, $currentMonth, $currentYear);
$stmt->execute();
$budgetResult = $stmt->get_result();
$budget = $budgetResult->num_rows > 0 ? $budgetResult->fetch_assoc()['budget_amount'] : 0;

// Get remaining budget
$remaining = $budget - $monthlyTotal;

// Get total expenses (all time)
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$totalExpenses = $stmt->get_result()->fetch_assoc()['total'];

// Get expenses by category for current month
$stmt = $conn->prepare("
    SELECT 
        c.name,
        c.color,
        COALESCE(SUM(e.amount), 0) as total
    FROM categories c
    LEFT JOIN expenses e ON c.id = e.category_id 
        AND e.user_id = ? 
        AND MONTH(e.expense_date) = ? 
        AND YEAR(e.expense_date) = ?
    WHERE c.user_id = ?
    GROUP BY c.id, c.name, c.color
    ORDER BY total DESC
");
$stmt->bind_param("iiii", $userId, $currentMonth, $currentYear, $userId);
$stmt->execute();
$categoryExpenses = $stmt->get_result();

// Get recent expenses
$stmt = $conn->prepare("
    SELECT 
        e.id,
        e.amount,
        e.description,
        e.expense_date,
        c.name as category_name,
        c.color
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = ?
    ORDER BY e.expense_date DESC
    LIMIT 5
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$recentExpenses = $stmt->get_result();

$pageTitle = 'لوحة التحكم';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-card">
            <div class="hero-grid">
                <div>
                    <h2>مرحباً، <?php echo htmlspecialchars(getUserName()); ?></h2>
                    <p>إليك ملخص نفقاتك الشهرية والميزانية المتبقية. يمكنك إدارة مصروفاتك وتتبع إنفاقك بسهولة.</p>
                    <div class="hero-badges">
                        <a href="expenses.php" class="btn btn-primary">إضافة مصروف</a>
                        <a href="budget.php" class="btn btn-secondary">تعديل الميزانية</a>
                    </div>
                </div>
                <div class="hero-panel">
                    <div class="mini-card">
                        <div>
                            <strong><?php echo number_format($monthlyTotal, 2); ?> ر.س</strong>
                            <span>إجمالي هذا الشهر</span>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div>
                            <strong><?php echo number_format($budget, 2); ?> ر.س</strong>
                            <span>الميزانية المخطط لها</span>
                        </div>
                    </div>
                    <div class="mini-card">
                        <div>
                            <strong><?php echo number_format(max(0, $remaining), 2); ?> ر.س</strong>
                            <span><?php echo $remaining >= 0 ? 'المتبقي' : 'تجاوز الميزانية'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="section">
        <div class="section-head">
            <h3>الإحصائيات</h3>
            <p><?php echo date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)); ?></p>
        </div>

        <div class="grid-3">
            <div class="stat">
                <div class="label">إجمالي المصروفات</div>
                <div class="value"><?php echo number_format($monthlyTotal, 2); ?></div>
                <div class="hint">ر.س</div>
            </div>

            <div class="stat">
                <div class="label">الميزانية</div>
                <div class="value"><?php echo number_format($budget, 2); ?></div>
                <div class="hint">ر.س</div>
            </div>

            <div class="stat">
                <div class="label">المتبقي</div>
                <div class="value" style="color: <?php echo $remaining >= 0 ? '#10b981' : '#ef4444'; ?>">
                    <?php echo number_format(abs($remaining), 2); ?>
                </div>
                <div class="hint">ر.س</div>
            </div>
        </div>
    </section>

    <!-- Categories Breakdown -->
    <section class="section">
        <div class="section-head">
            <h3>توزيع المصروفات حسب التصنيف</h3>
            <p>نسبة الإنفاق على كل فئة</p>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>التصنيف</th>
                        <th>المبلغ</th>
                        <th>النسبة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalByCategory = 0;
                    $categories = [];
                    
                    // First pass to get total
                    $categoryExpenses->data_seek(0);
                    while ($row = $categoryExpenses->fetch_assoc()) {
                        $totalByCategory += $row['total'];
                        $categories[] = $row;
                    }

                    // Second pass to display
                    foreach ($categories as $cat):
                        $percentage = $totalByCategory > 0 ? ($cat['total'] / $totalByCategory) * 100 : 0;
                    ?>
                        <tr>
                            <td>
                                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 3px; background-color: <?php echo htmlspecialchars($cat['color']); ?>; margin-left: 8px;"></span>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </td>
                            <td><?php echo number_format($cat['total'], 2); ?> ر.س</td>
                            <td><?php echo number_format($percentage, 1); ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Recent Expenses -->
    <section class="section">
        <div class="section-head">
            <h3>آخر المصروفات</h3>
            <a href="expenses.php" class="btn btn-secondary" style="padding: 8px 12px; font-size: 0.9rem;">عرض الكل</a>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>التصنيف</th>
                        <th>الوصف</th>
                        <th>المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($expense = $recentExpenses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($expense['expense_date'])); ?></td>
                            <td>
                                <span style="display: inline-block; width: 12px; height: 12px; border-radius: 3px; background-color: <?php echo htmlspecialchars($expense['color']); ?>; margin-left: 8px;"></span>
                                <?php echo htmlspecialchars($expense['category_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($expense['description'] ?? '-'); ?></td>
                            <td><?php echo number_format($expense['amount'], 2); ?> ر.س</td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
