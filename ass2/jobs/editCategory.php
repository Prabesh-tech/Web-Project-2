<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

/* ---------------- LOGIN CHECK ---------------- */
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

/* ---------------- ADMIN CHECK ---------------- */
if (empty($_SESSION['user']['isAdmin']) || !in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) {
    die("Access denied: Admin only");
}

/* ---------------- ID CHECK ---------------- */
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    die("Invalid category ID");
}

/* ---------------- FETCH CATEGORY ---------------- */
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$category) {
    die("Category not found");
}

/* ---------------- UPDATE CATEGORY ---------------- */
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $error = "Category name cannot be empty!";
    } else {
        $update = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
        $update->execute([$name, $description, $id]);
        $success = true;

        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

require 'includes/header.php';
?>

<div class="page-section">
    <div class="auth-card">
        <h2>Edit Category</h2>

        <?php if ($success): ?>
            <div class="auth-success">✅ Category updated successfully.</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>Category Name</label>
            <input type="text"
                   name="name"
                   value="<?= htmlspecialchars($category['name']) ?>"
                   required>

            <label>Description</label>
            <input type="text"
                   name="description"
                   value="<?= htmlspecialchars($category['description'] ?? '') ?>">

            <button type="submit" class="btn-login">
                Update Category
            </button>
        </form>

        <a href="adminCategories.php" class="btn-back">← Back</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>