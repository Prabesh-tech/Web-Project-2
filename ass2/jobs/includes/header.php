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
    'Job Categories' => '#',
    'Services' => '#',
    'Blogs' => '#',
];

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
<title>Prabesh Job</title>
<link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header class="site-header">
    <div class="header-inner">

        <!-- LOGO -->
        <a href="index.php" class="logo">Prabesh Job</a>

        <!-- NAV -->
        <nav class="main-nav">
            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Job Categories</button>
                <div class="nav-dropdown-menu nav-dropdown-menu-columns">
                    <a href="#">Sales & Marketing</a>
                    <a href="#">Education</a>
                    <a href="#">General Mgmt</a>
                    <a href="#">Sales</a>
                    <a href="#">Customer Service</a>
                    <a href="#">IT – Programming & Development</a>
                    <a href="#">Health/Pharma/Biotech/Medical/R&D</a>
                    <a href="#">Creative / Graphics / Designing</a>
                    <a href="#">Abroad Study</a>
                    <a href="#">Advertising</a>
                    <a href="#">Nursing</a>
                    <a href="#">Travel And Tourism</a>
                    <a href="#">Human Resource</a>
                </div>
            </div>

            <div class="nav-dropdown">
                <button class="nav-link nav-dropdown-toggle" type="button">Services</button>
                <div class="nav-dropdown-menu">
                    <a href="#">Vacancy Announcement & Management Tools</a>
                    <a href="#">Recruitment Services</a>
                    <a href="#">Outsourcing Tools & Services</a>
                    <a href="#">Human Resource Consulting</a>
                </div>
            </div>
            <a href="#" class="nav-link">Blogs</a>
            <a href="#about" class="nav-link">About Us</a>
            <a href="#contact" class="nav-link">Contact Us</a>

            <?php foreach ($navItems as $label => $path):
                if (in_array($label,['Job Categories','Services','Blogs'], true)) {
                    continue;
                }
            ?>
                <a href="<?= htmlspecialchars($path) ?>" class="nav-link">
                    <?= htmlspecialchars($label) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- PROMINENT ADD JOB BUTTON (normal users only; admins see dashboard) -->
        <?php if (isset($_SESSION['user'])):
            $isAdminUser = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
                           (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
        ?>
            <?php if ($isAdminUser): ?>
                <a href="admin.php" class="btn-addauction admin-dashboard">Admin Dashboard</a>
            <?php else: ?>
                <a href="jobs/addJob.php" class="btn-addauction">Add Job</a>
            <?php endif; ?>
        <?php endif; ?>

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
</header>

<main class="page-body">