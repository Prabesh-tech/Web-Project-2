<?php
/**
 * Form Layout - For forms like add/edit job, add/edit category, login, register
 * Features: Centered form container, simple focused design
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Form - Prabesh Job' ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/form-style.css">
</head>
<body class="form-layout">
    <!-- HEADER (optional for forms) -->
    <?php if ($includeHeader ?? true): ?>
        <div class="form-header">
            <a href="index.php" class="form-logo">Prabesh Job</a>
        </div>
    <?php endif; ?>

    <!-- FORM CONTAINER -->
    <main class="form-main">
        <div class="form-container">
            <!-- FORM TITLE & DESCRIPTION -->
            <div class="form-intro">
                <h1><?= $formTitle ?? 'Form' ?></h1>
                <?php if (!empty($formDescription)): ?>
                    <p class="form-description"><?= htmlspecialchars($formDescription) ?></p>
                <?php endif; ?>
            </div>

            <!-- ERROR/SUCCESS MESSAGES -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <strong>Success:</strong> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <!-- FORM CONTENT -->
            <div class="form-content">
                <?= $content ?? '' ?>
            </div>

            <!-- FORM FOOTER (back links, etc.) -->
            <?php if (!empty($formFooter)): ?>
                <div class="form-footer">
                    <?= $formFooter ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <?php if ($includeFooter ?? false): ?>
        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <?php endif; ?>
</body>
</html>
