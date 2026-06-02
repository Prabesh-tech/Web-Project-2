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
        $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $categories = [];
}

// Determine if user is admin
$isAdmin = isset($_SESSION['user']) && !empty($_SESSION['user']['isAdmin']) 
           && in_array(intval($_SESSION['user']['isAdmin']), [1, 2], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Prabesh Job - Find Your Perfect Career' ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <!-- HEADER -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <!-- PAGE CONTENT -->
    <main class="page-body">
        <div class="container-main">
            <?php if (!empty($breadcrumbs)): ?>
                <nav class="breadcrumbs">
                    <?php foreach ($breadcrumbs as $label => $url): ?>
                        <a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a>
                        <span class="separator">/</span>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <div class="main-content">
                <?= $content ?? '' ?>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
