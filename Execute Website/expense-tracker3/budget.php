<?php
/**
 * Budget Page
 * صفحة الميزانية
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();
$userId = getCurrentUserId();
$pageTitle = 'الميزانية';
$errors = [];
$success = '';
$currentMonth = date('m');
$currentYear = date('Y');
$budgetAmount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'set_budget') {
    $budgetAmount = $_POST['budget_amount'] ?? '';

    if (empty($budgetAmount) || !is_numeric($budgetAmount) || $budgetAmount < 0) {
        $errors[] = 'يرجى إدخال مبلغ ميزانية صالح.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM budgets WHERE userId = ? AND month = ? AND year = ?");
        $stmt->bind_param('iii', $userId, $currentMonth, $currentYear);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $budgetRow = $result->fetch_assoc();
            $stmt->close();
            $stmt = $conn->prepare("UPDATE budgets SET budgetAmount = ?, updatedAt = NOW() WHERE id = ?");
            $stmt->bind_param('di', $budgetAmount, $budgetRow['id']);
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO budgets (userId, month, year, budgetAmount, createdAt, updatedAt) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param('iiid', $userId, $currentMonth, $currentYear, $budgetAmount);
        }

        if ($stmt->execute()) {
            $success = 'تم حفظ الميزانية لهذا الشهر بنجاح.';
        } else {
            $errors[] = 'حدث خطأ أثناء حفظ الميزانية. حاول مرة أخرى.';
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT budgetAmount FROM budgets WHERE userId = ? AND month = ? AND year = ?");
$stmt->bind_param('iii', $userId, $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $budgetAmount = $row['budgetAmount'];
}
$stmt->close();

$pastBudgets = [];
$stmt = $conn->prepare("SELECT month, year, budgetAmount FROM budgets WHERE userId = ? ORDER BY year DESC, month DESC LIMIT 6");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pastBudgets[] = $row;
}
$stmt->close();

require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div class="section-head">
            <h2>الميزانية</h2>
            <p>حدد الميزانية الشهرية وتابعها مقابل نفقاتك.</p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 32px;">
            <h3>ميزانية الشهر الحالي (<?php echo date('F Y'); ?>)</h3>
            <form method="POST" style="max-width: 420px;">
                <input type="hidden" name="action" value="set_budget">
                <div class="form-group">
                    <label for="budget_amount">المبلغ المخطط</label>
                    <input type="number" step="0.01" id="budget_amount" name="budget_amount" value="<?php echo htmlspecialchars($budgetAmount); ?>" placeholder="0.00" required>
                </div>
                <button type="submit" class="btn btn-primary">حفظ الميزانية</button>
            </form>
        </div>

        <div class="card">
            <h3>آخر الميزانيات</h3>
            <?php if (!empty($pastBudgets)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>السنة</th>
                            <th>الميزانية</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pastBudgets as $budgetRow): ?>
                            <tr>
                                <td><?php echo htmlspecialchars(DateTime::createFromFormat('!m', $budgetRow['month'])->format('F')); ?></td>
                                <td><?php echo htmlspecialchars($budgetRow['year']); ?></td>
                                <td><?php echo number_format($budgetRow['budgetAmount'], 2); ?> ر.س</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>لم يتم إعداد ميزانية بعد.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php';

