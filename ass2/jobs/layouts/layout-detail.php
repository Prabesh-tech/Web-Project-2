<?php
/**
 * Detail Layout - For single job/item detail pages
 * Features: Full-width with sidebar, breadcrumbs, related items
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
    <title><?= $pageTitle ?? 'Details - Prabesh Job' ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/detail-style.css">
    <link rel="stylesheet" href="assets/css/visibility-fix.css">
    <link rel="stylesheet" href="assets/css/dropdown-add-button.css">
</head>
<body>
    <!-- HEADER -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <!-- BREADCRUMBS -->
    <?php if (!empty($breadcrumbs)): ?>
        <nav class="detail-breadcrumbs">
            <div class="container-main">
                <?php foreach ($breadcrumbs as $label => $url): ?>
                    <a href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($label) ?></a>
                    <span class="separator">/</span>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>

    <!-- MAIN CONTENT -->
    <main class="detail-main">
        <div class="container-main">
            <div class="detail-wrapper">
                <!-- PRIMARY CONTENT -->
                <div class="detail-primary">
                    <?= $content ?? '' ?>
                </div>

                <!-- SIDEBAR (optional) -->
                <?php if (!empty($sidebar)): ?>
                    <aside class="detail-sidebar">
                        <?= $sidebar ?>
                    </aside>
                <?php endif; ?>
            </div>

            <!-- RELATED ITEMS -->
            <?php if (!empty($related)): ?>
                <section class="detail-related">
                    <h2>Related Items</h2>
                    <div class="related-grid">
                        <?= $related ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <?php if ($showFooter ?? true): ?>
        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <?php endif; ?>
</body>
</html>
