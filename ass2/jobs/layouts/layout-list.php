<?php
/**
 * List Layout - For jobs/items listing pages with filters
 * Features: Sidebar filters, grid/list view, pagination
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/DbConnection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Listings - Prabesh Job' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/list-style.css">
    <link rel="stylesheet" href="assets/css/visibility-fix.css">
    <link rel="stylesheet" href="assets/css/dropdown-add-button.css">
</head>
<body>
    <!-- HEADER -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <!-- BREADCRUMBS -->
    <?php if (!empty($breadcrumbs)): ?>
        <nav class="list-breadcrumbs">
            <div class="container-main">
                <?php foreach ($breadcrumbs as $label => $url): ?>
                    <a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a>
                    <span class="separator">/</span>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <!-- MAIN CONTENT -->
    <main class="list-main">
        <div class="container-main">
            <div class="list-wrapper">
                <!-- SIDEBAR FILTERS -->
                <?php if (!empty($filters)): ?>
                    <aside class="list-sidebar">
                        <div class="filters-header">
                            <h2>Filters</h2>
                            <a href="<?= $_SERVER['REQUEST_URI'] ?>" class="reset-filters">Clear</a>
                        </div>
                        <div class="filters-content">
                            <?= $filters ?>
                        </div>
                    </aside>
                <?php endif; ?>

                <!-- LIST CONTENT -->
                <div class="list-content">
                    <!-- RESULTS HEADER -->
                    <div class="list-header">
                        <h1><?= $contentTitle ?? 'Results' ?></h1>
                        <div class="list-controls">
                            <?php if (!empty($controls)): ?>
                                <?= $controls ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ITEMS LIST -->
                    <div class="list-items">
                        <?= $content ?? '' ?>
                    </div>

                    <!-- PAGINATION -->
                    <?php if (!empty($pagination)): ?>
                        <div class="list-pagination">
                            <?= $pagination ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <?php if ($showFooter ?? true): ?>
        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <?php endif; ?>
</body>
</html>
