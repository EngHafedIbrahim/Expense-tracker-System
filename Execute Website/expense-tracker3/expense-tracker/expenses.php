<?php
/**
 * Expenses Page
 * صفحة المصروفات
 */

require_once 'includes/app.php';

initPage('المصروفات');
$userId = getCurrentUserId();
$pageTitle = 'المصروفات';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_expense') {
    $categoryId = $_POST['category_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $description = sanitize($_POST['description'] ?? '');
    $expenseDate = $_POST['expense_date'] ?? date('Y-m-d');

    if (empty($categoryId) || !is_numeric($categoryId)) {
        $errors[] = 'يرجى اختيار تصنيف صالح.';
    }

    if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
        $errors[] = 'يرجى إدخال مبلغ صالح أكبر من صفر.';
    }

    if (empty($expenseDate)) {
        $errors[] = 'يرجى تحديد تاريخ المصروف.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO expenses (userId, categoryId, amount, description, expenseDate, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('iisss', $userId, $categoryId, $amount, $description, $expenseDate);

        if ($stmt->execute()) {
            $success = 'تم إضافة المصروف بنجاح.';
        } else {
            $errors[] = 'حدث خطأ أثناء حفظ المصروف. يرجى المحاولة لاحقاً.';
        }

        $stmt->close();
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $expenseId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND userId = ?");
    $stmt->bind_param('ii', $expenseId, $userId);
    if ($stmt->execute()) {
        $success = 'تم حذف المصروف بنجاح.';
    } else {
        $errors[] = 'فشل حذف المصروف. تأكد من صحة البيانات ثم أعد المحاولة.';
    }
    $stmt->close();
}

$categories = [];
$stmt = $conn->prepare("SELECT id, name, color FROM categories WHERE userId = ? ORDER BY name ASC");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

$expenses = [];
$stmt = $conn->prepare("SELECT e.id, e.amount, e.description, e.expenseDate, c.name AS category_name, c.color FROM expenses e JOIN categories c ON e.categoryId = c.id WHERE e.userId = ? ORDER BY e.expenseDate DESC LIMIT 100");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $expenses[] = $row;
}
$stmt->close();

require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div class="section-head">
            <h2>المصروفات</h2>
            <p>أضف مصروفاً جديداً واعرض قائمة مصروفاتك الأخيرة.</p>
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

        <div class="grid-2 gap-24" style="margin-bottom: 32px;">
            <div class="card">
                <h3>إضافة مصروف جديد</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_expense">
                    <div class="form-group">
                        <label for="category_id">التصنيف</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">اختر التصنيف</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">المبلغ</label>
                        <input type="number" step="0.01" id="amount" name="amount" placeholder="0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="expense_date">تاريخ المصروف</label>
                        <input type="date" id="expense_date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">الوصف</label>
                        <textarea id="description" name="description" rows="3" placeholder="وصف المصروف (اختياري)"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ المصروف</button>
                </form>

                <?php if (empty($categories)): ?>
                    <div class="info-card" style="margin-top: 16px;">
                        لا توجد لديك تصنيفات بعد. <a href="<?php echo SITE_URL; ?>/categories.php">أضف تصنيفاً أولاً</a> لتتمكن من حفظ المصروفات.
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>آخر المصروفات</h3>
                <?php if (!empty($expenses)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>التاريخ</th>
                                <th>التصنيف</th>
                                <th>الوصف</th>
                                <th>المبلغ</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expenses as $expense): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($expense['expenseDate'])); ?></td>
                                    <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
                                    <td><?php echo htmlspecialchars($expense['description']); ?></td>
                                    <td><?php echo number_format($expense['amount'], 2); ?> ر.س</td>
                                    <td>
                                        <a href="?delete=<?php echo $expense['id']; ?>" data-confirm="هل تريد حذف هذا المصروف؟" class="btn btn-danger btn-sm">حذف</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>لا توجد مصروفات مسجلة حتى الآن.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php';

