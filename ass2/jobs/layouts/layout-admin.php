<?php
/**
 * Admin Layout - For admin dashboard and management pages
 * Features: Sidebar navigation, admin-specific styling
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin access
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1, 2], true)) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../includes/DbConnection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Dashboard - Prabesh Job' ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/admin-style.css">
</head>
<body class="admin-layout">
    <div class="admin-wrapper">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="admin.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'active' : '' ?>">
                    <span class="icon">📊</span> Dashboard
                </a>
                <a href="adminCategories.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) === 'adminCategories.php' ? 'active' : '' ?>">
                    <span class="icon">📁</span> Manage Categories
                </a>
                <a href="adminBrands.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) === 'adminBrands.php' ? 'active' : '' ?>">
                    <span class="icon">🏷️</span> Manage Brands
                </a>
                <a href="manageAdmins.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) === 'manageAdmins.php' ? 'active' : '' ?>">
                    <span class="icon">👥</span> Manage Admins
                </a>
                <a href="logout.php" class="sidebar-link logout">
                    <span class="icon">🚪</span> Logout
                </a>
            </nav>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main">
            <!-- ADMIN HEADER -->
            <div class="admin-header">
                <h1><?= $pageTitle ?? 'Admin Panel' ?></h1>
                <div class="admin-user">
                    <span><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Admin') ?></span>
                </div>
            </div>

            <!-- BREADCRUMBS -->
            <?php if (!empty($breadcrumbs)): ?>
                <nav class="admin-breadcrumbs">
                    <?php foreach ($breadcrumbs as $label => $url): ?>
                        <a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a>
                        <span class="separator">/</span>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <!-- PAGE CONTENT -->
            <div class="admin-content">
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
</body>
</html>
