<?php
/**
 * Categories Page
 * صفحة التصنيفات
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

requireLogin();
$userId = getCurrentUserId();
$pageTitle = 'التصنيفات';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_category') {
    $name = sanitize($_POST['name'] ?? '');
    $icon = sanitize($_POST['icon'] ?? '');
    $color = sanitize($_POST['color'] ?? '#2563eb');

    if (empty($name)) {
        $errors[] = 'يرجى إدخال اسم التصنيف.';
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO categories (userId, name, icon, color, createdAt, updatedAt) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param('isss', $userId, $name, $icon, $color);

        if ($stmt->execute()) {
            $success = 'تم إضافة التصنيف بنجاح.';
        } else {
            $errors[] = 'فشل إضافة التصنيف. قد يكون الاسم موجوداً بالفعل.';
        }

        $stmt->close();
    }
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $categoryId = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND userId = ?");
    $stmt->bind_param('ii', $categoryId, $userId);
    if ($stmt->execute()) {
        $success = 'تم حذف التصنيف بنجاح.';
    } else {
        $errors[] = 'حدث خطأ أثناء حذف التصنيف. تأكد من عدم وجود مصروفات مرتبطة به.';
    }
    $stmt->close();
}

$categories = [];
$stmt = $conn->prepare("SELECT id, name, icon, color FROM categories WHERE userId = ? ORDER BY name ASC");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

require_once 'includes/header.php';
?>

<div class="container">
    <section class="section">
        <div class="section-head">
            <h2>التصنيفات</h2>
            <p>أنشئ تصنيفات جديدة ونظّم مصروفاتك بسهولة.</p>
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
                <h3>إضافة تصنيف</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add_category">
                    <div class="form-group">
                        <label for="name">اسم التصنيف</label>
                        <input type="text" id="name" name="name" required placeholder="مثل: طعام، مواصلات">
                    </div>
                    <div class="form-group">
                        <label for="icon">أيقونة</label>
                        <input type="text" id="icon" name="icon" placeholder="اختياري - مثل: 🍔">
                    </div>
                    <div class="form-group">
                        <label for="color">لون التصنيف</label>
                        <input type="color" id="color" name="color" value="#2563eb">
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ التصنيف</button>
                </form>
            </div>

            <div class="card">
                <h3>التصنيفات الحالية</h3>
                <?php if (!empty($categories)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>الأيقونة</th>
                                <th>اللون</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td><?php echo htmlspecialchars($category['icon']); ?></td>
                                    <td><span style="display:inline-block;width:20px;height:20px;border-radius:4px;background:<?php echo htmlspecialchars($category['color']); ?>;"></span> <?php echo htmlspecialchars($category['color']); ?></td>
                                    <td>
                                        <a href="?delete=<?php echo $category['id']; ?>" data-confirm="هل تريد حذف هذا التصنيف؟" class="btn btn-danger btn-sm">حذف</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>لا توجد تصنيفات حتى الآن.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php';

