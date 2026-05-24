<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

if (!isset($_SESSION['user']['id']) || 
   ($_SESSION['user']['role'] !== 'Admin' && $_SESSION['user']['role'] !== 'Super Admin')) {
    header("Location: login.php");
    exit;
}

$username = htmlspecialchars($_SESSION['user']['username']);
$role = $_SESSION['user']['role'] ?? 'User';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>
<div class="admin-page">
    <div class="admin-card admin-dashboard-card">
        <a href="index.php" class="btn-back admin-back-link">← Back to Home</a>
        <div class="admin-welcome">
            <p class="admin-welcome-label">Welcome,</p>
            <h2><?= $username ?> <span class="admin-role">(<?= $role ?>)</span></h2>
        </div>

        <div class="admin-actions">
            <a class="admin-action" href="addAuction.php">
                <span><span class="admin-icon">➕</span> Add Auction</span>
                <span class="admin-action-note">List a car</span>
            </a>

            <?php if ($role === 'Super Admin'): ?>
                <a class="admin-action" href="addAdmin.php">
                    <span><span class="admin-icon">➕</span> Create New Admin</span>
                    <span class="admin-action-note">Super Admin</span>
                </a>
            <?php endif; ?>

            <a class="admin-action" href="adminCategories.php">
                <span><span class="admin-icon">📂</span> Manage Categories</span>
                <span class="admin-action-note">Categories</span>
            </a>

            <a class="admin-action" href="adminBrands.php">
                <span><span class="admin-icon">🏎️</span> Manage Brands</span>
                <span class="admin-action-note">Brands</span>
            </a>

            <a class="admin-action admin-logout" href="logout.php">
                <span><span class="admin-icon">🚪</span> Logout</span>
                <span class="admin-action-note">Exit</span>
            </a>
        </div>
    </div>

</div>
</body>
</html>
