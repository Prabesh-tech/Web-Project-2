<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

//  Only allow admins or super admin
if (!isset($_SESSION['user']['id']) || 
   ($_SESSION['user']['role'] !== 'Admin' && $_SESSION['user']['role'] !== 'Super Admin')) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = false;

//  Handle Add Category
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $categoryName = trim($_POST['categoryName'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($categoryName === '') {
        $error = 'Category name is required.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
            $check->execute([$categoryName]);

            if ($check->rowCount() > 0) {
                $error = 'This category already exists - cannot add duplicates.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO categories (name, description) VALUES (?, ?)'
                );
                $stmt->execute([$categoryName, $description]);
                $success = true;
            }
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}

//  Handle Delete Category
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        $success = true;
    } catch (PDOException $e) {
        $error = "DB Error: " . $e->getMessage();
    }
}

//  Handle Edit Category
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = intval($_POST['id']);
    $categoryName = trim($_POST['categoryName'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($categoryName === '') {
        $error = 'Category name is required.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM categories WHERE name = ? AND id != ?');
            $check->execute([$categoryName, $id]);
            if ($check->rowCount() > 0) {
                $error = 'This category already exists. Please use the existing category.';
            } else {
                $stmt = $pdo->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
                $stmt->execute([$categoryName, $description, $id]);
                $success = true;
            }
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}

//  Fetch all categories
$categories = [];
try {
    $stmt = $pdo->query('SELECT id, name, description FROM categories ORDER BY id DESC');
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "DB Error: " . $e->getMessage();
}

$username = htmlspecialchars($_SESSION['user']['username']);
$role = $_SESSION['user']['role'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>
<div class="admin-page">
    <div class="admin-card">
        <h2>Welcome, <?= $username ?> (<?= $role ?>)</h2>
        <h3>Add Car Category</h3>

        <?php if ($success): ?>
            <div class="auth-success">✅ Action completed successfully!</div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="auth-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Add Category Form -->
        <form method="POST" class="admin-form">
            <input type="hidden" name="action" value="add">
            <input type="text" name="categoryName" placeholder="Category Name" required>
            <input type="text" name="description" placeholder="Description (optional)">
            <button type="submit" class="btn-admin">Add Category</button>
        </form>

        <h3>Existing Categories</h3>
        <?php if (!empty($categories)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= htmlspecialchars($cat['id']) ?></td>
                            <td><?= htmlspecialchars($cat['name']) ?></td>
                            <td><?= htmlspecialchars($cat['description']) ?></td>
                            <td class="admin-table-actions">
                                <a href="editCategory.php?id=<?= $cat['id'] ?>" class="btn-admin btn-small">✏️ Edit</a>
                                <form method="POST" onsubmit="return confirm('Delete this category?');" class="inline-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn-admin btn-small btn-delete">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No categories found.</p>
        <?php endif; ?>

        <a href="admin.php" class="admin-back">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
