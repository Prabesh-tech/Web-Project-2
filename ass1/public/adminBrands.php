<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

// Only allow admins or super admin
if (!isset($_SESSION['user']['id']) || 
   ($_SESSION['user']['role'] !== 'Admin' && $_SESSION['user']['role'] !== 'Super Admin')) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = false;

// Handle Add Brand
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $brandName = trim($_POST['brandName'] ?? '');
    $isTopBrand = isset($_POST['isTopBrand']) ? 1 : 0;

    if ($brandName === '') {
        $error = 'Brand name is required.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM brands WHERE name = ?');
            $check->execute([$brandName]);

            if ($check->rowCount() > 0) {
                $error = 'This brand already exists - cannot add duplicates.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO brands (name, isTopBrand) VALUES (?, ?)'
                );
                $stmt->execute([$brandName, $isTopBrand]);
                $success = true;
            }
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}

// Handle Delete Brand
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare('DELETE FROM brands WHERE id = ?');
        $stmt->execute([$id]);
        $success = true;
    } catch (PDOException $e) {
        $error = "DB Error: " . $e->getMessage();
    }
}

// Handle Toggle Top Brand
if (isset($_POST['action']) && $_POST['action'] === 'toggle') {
    $id = intval($_POST['id']);
    try {
        $stmt = $pdo->prepare('UPDATE brands SET isTopBrand = NOT isTopBrand WHERE id = ?');
        $stmt->execute([$id]);
        $success = true;
    } catch (PDOException $e) {
        $error = "DB Error: " . $e->getMessage();
    }
}

// Fetch all brands
$stmt = $pdo->query('SELECT * FROM brands ORDER BY isTopBrand DESC, name ASC');
$brands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Brands – Admin</title>
    <link rel="stylesheet" href="assets/carbuy.css">
</head>
<body style="background: #080808;">

<header class="site-header">
    <div class="header-inner">
        <a href="index.php" class="logo">CarBuy</a>
        <div class="user-area">
            <span class="username">Hi, <?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?></span>
            <a href="logout.php" class="user-btn">Logout</a>
        </div>
    </div>
</header>

<div class="admin-container">
    <div class="admin-header">
        <h1>🏎️ Manage Brands</h1>
        <a href="admin.php" class="btn-back">← Back to Admin</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">✓ Brand operation successful!</div>
    <?php endif; ?>

    <div class="admin-form">
        <h2>Add New Brand</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="form-group">
                <label for="brandName">Brand Name *</label>
                <input type="text" id="brandName" name="brandName" required placeholder="e.g., Tesla, Lamborghini">
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="isTopBrand" name="isTopBrand">
                <label for="isTopBrand">✨ Mark as Top Brand (shows in main bar)</label>
            </div>

            <button type="submit" class="btn-add">➕ Add Brand</button>
        </form>
    </div>

    <table class="management-table">
        <thead>
            <tr>
                <th>Brand Name</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($brands)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #8f8f8f; padding: 30px;">No brands yet</td>
                </tr>
            <?php else: ?>
                <?php foreach ($brands as $brand): ?>
                    <tr>
                        <td><?= htmlspecialchars($brand['name']) ?></td>
                        <td>
                            <?php if ($brand['isTopBrand']): ?>
                                <span class="badge-top">⭐ Top Brand</span>
                            <?php else: ?>
                                <span class="badge-more">More Brands</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $brand['id'] ?>">
                                <button type="submit" class="btn-toggle">
                                    <?= $brand['isTopBrand'] ? '⬇ Move to More' : '⬆ Move to Top' ?>
                                </button>
                            </form>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $brand['id'] ?>">
                                <button type="submit" class="btn-delete" onclick="return confirm('Delete this brand?')">🗑 Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> CarBuy</p>
</footer>

</body>
</html>
