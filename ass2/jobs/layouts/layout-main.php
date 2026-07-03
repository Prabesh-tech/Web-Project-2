<?php
/**
 * Main Layout - Default layout for public-facing pages
 * Usage: Used for home, job listings, search pages
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/DbConnection.php';

// Fetch categories for nav
$categories = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY CASE WHEN name IN ('Sales & Marketing','Sales/Business Development','Sales','Information Technology','IT – Programming & Development','Human Resource') THEN 0 ELSE 1 END, name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $categories = [];
}

// Determine if user is admin
$isAdmin = isset($_SESSION['user']) && isset($_SESSION['user']['role']) 
           && in_array(intval($_SESSION['user']['role']), [1, 2], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Prabesh Job - Find Your Perfect Career' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/training-style.css">
    <link rel="stylesheet" href="assets/css/visibility-fix.css">
    <link rel="stylesheet" href="assets/css/about-visibility.css">
    <link rel="stylesheet" href="assets/css/dropdown-add-button.css">
</head>
<body>
    <!-- HEADER -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <!-- PAGE CONTENT -->
    <main class="page-body">
        <div class="container-main">
            <?php if (!empty($breadcrumbs) && count($breadcrumbs) > 1): ?>
                <nav class="breadcrumbs">
                    <?php $count = count($breadcrumbs); $index = 0; ?>
                    <?php foreach ($breadcrumbs as $label => $url): ?>
                        <?php $index++; ?>
                        <a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a>
                        <?php if ($index < $count): ?>
                            <span class="separator">/</span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <div class="main-content">
                <?= $content ?? '' ?>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <?php if ($showFooter ?? true): ?>
        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <?php endif; ?>
</body>

<script>
function toggleAddDropdown() {
    const menu = document.getElementById('addDropdownMenu');
    const btn = document.querySelector('.dropdown-btn');
    if (menu) {
        menu.classList.toggle('active');
        btn?.classList.toggle('active');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const container = document.querySelector('.add-dropdown-container');
    if (container && !container.contains(event.target)) {
        const menu = document.getElementById('addDropdownMenu');
        const btn = document.querySelector('.dropdown-btn');
        if (menu && menu.classList.contains('active')) {
            menu.classList.remove('active');
            btn?.classList.remove('active');
        }
    }
});
</script>
</html>
