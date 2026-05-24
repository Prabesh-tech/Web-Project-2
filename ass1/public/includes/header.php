<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/DbConnection.php';

$currentPage = basename($_SERVER['PHP_SELF']);

/* FETCH CATEGORIES */
$categories = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $categories = [];
}

/* NAV ITEMS */
$navItems = [
    'Home'   => 'index.php',
    'Search' => 'search.php',
];

// Note: 'Create Auction' is shown as a prominent button in the header for logged-in users

// Admin-only items
if (isset($_SESSION['user']) && !empty($_SESSION['user']['isAdmin'])) {
    $navItems['Admin Dashboard'] = 'admin.php';
    $navItems['Manage Categories'] = 'adminCategories.php';
    $navItems['Manage Admins']     = 'manageAdmins.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CarBuy</title>
<link rel="stylesheet" href="assets/carbuy.css">
</head>

<body>

<header class="site-header">
    <div class="header-inner">

        <!-- LOGO -->
        <a href="index.php" class="logo">CARBUY</a>

        <!-- PROMINENT ADD AUCTION BUTTON (normal users only; admins see dashboard) -->
        <?php if (isset($_SESSION['user'])):
            $isAdminUser = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
                           (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
        ?>
            <?php if ($isAdminUser): ?>
                <a href="admin.php" class="btn-addauction admin-dashboard">Admin Dashboard</a>
            <?php else: ?>
                <a href="addAuction.php" class="btn-addauction">Add Auction</a>
            <?php endif; ?>
        <?php endif; ?>

        <!-- NAV -->
        <nav class="nav">
            <?php foreach ($navItems as $label => $path): ?>
                <a href="<?= htmlspecialchars($path) ?>"
                   class="nav-link <?= $currentPage === basename($path) ? 'active' : '' ?>">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- USER SECTION -->
        <div class="user-tools">

            <?php if (isset($_SESSION['user'])): ?>

                <span class="username">
                    Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?>
                    <?php if (!empty($_SESSION['user']['role'])): ?>
                        <span class="user-role">(<?= htmlspecialchars($_SESSION['user']['role']) ?>)</span>
                    <?php endif; ?>
                </span>

                <a href="logout.php" class="btn-logout">Logout</a>

            <?php else: ?>

                <!-- LOGIN ICON (CLICKABLE) -->
                <a href="login.php" class="user-icon" title="Login">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                    </svg>
                </a>

                <a href="login.php" class="btn-hero">Login</a>
                <a href="register.php" class="btn-hero outline">Register</a>

            <?php endif; ?>

        </div>
    </div>

    <!-- CATEGORY NAV -->
    <?php if (!empty($categories)): ?>
        <div class="cat-nav">
            <div class="cat-nav-inner">

                <a href="index.php"
                   class="cat-tab <?= !isset($_GET['id']) ? 'active' : '' ?>">
                   All
                </a>

                <?php foreach ($categories as $cat): ?>
                    <a href="category.php?id=<?= $cat['id'] ?>"
                       class="cat-tab <?= (isset($_GET['id']) && $_GET['id'] == $cat['id']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endif; ?>

</header>

<main class="page-body">