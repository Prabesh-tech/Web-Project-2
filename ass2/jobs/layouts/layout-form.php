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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form-style.css">
    <link rel="stylesheet" href="assets/css/visibility-fix.css">
    <link rel="stylesheet" href="assets/css/dropdown-add-button.css">
</head>
<body class="form-layout <?= $pageClass ?? '' ?>">
    <!-- HEADER (optional for forms) -->
    <?php if ($includeHeader ?? true): ?>
        <div class="form-header">
            <a href="index.php" class="form-logo">
                <img src="assets/images/Image3.png" alt="Prabesh Jobs" class="logo-img">
            </a>
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
                <?php $currentRole = $role ?? ($_GET['role'] ?? 'jobseeker'); ?>
                <?php $hideRoleSwitcher = $hideRoleSwitcher ?? false; ?>
                <?php $showRoleSwitcher = !$hideRoleSwitcher && $currentRole !== 'admin'; ?>
                <?php if ($showRoleSwitcher): ?>
                    <div class="role-switcher">
                        <a href="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'])) ?>?role=jobseeker" class="btn-role <?= $currentRole === 'jobseeker' ? 'btn-role-active' : '' ?>">Jobseeker</a>
                        <a href="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'])) ?>?role=employer" class="btn-role <?= $currentRole === 'employer' ? 'btn-role-active' : '' ?>">Employer</a>
                        <?php if (($allowAdminRole ?? true) === true): ?>
                            <a href="<?= htmlspecialchars(basename($_SERVER['PHP_SELF'])) ?>?role=admin" class="btn-role <?= $currentRole === 'admin' ? 'btn-role-active' : '' ?>">Admin</a>
                        <?php endif; ?>
                    </div>
                <?php elseif (!$hideRoleSwitcher && $currentRole === 'admin'): ?>
                    <div class="role-switcher">
                        <span class="btn-role btn-role-active">Admin</span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($formSwitcher)): ?>
                    <div class="form-switcher">
                        <?= $formSwitcher ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SUCCESS MESSAGES -->
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
            <?php if (!empty($formFooter) && ($currentRole ?? 'jobseeker') !== 'admin'): ?>
                <div class="form-footer">
                    <?= $formFooter ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <?php if ($includeFooter ?? false): ?>
           <?php if ($showFooter ?? true): ?>
              <?php require_once __DIR__ . '/../includes/footer.php'; ?>
           <?php endif; ?>
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

    <script>
        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.toggle-password');
            
            toggleButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const passwordInput = this.parentElement.querySelector('input[type="password"], input[type="text"]');
                    
                    if (passwordInput) {
                        const isPassword = passwordInput.type === 'password';
                        passwordInput.type = isPassword ? 'text' : 'password';
                        this.classList.toggle('active', !isPassword);
                    }
                });
            });
        });
    </script>
</body>
</html>
