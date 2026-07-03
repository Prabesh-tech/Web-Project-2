<form method="POST" class="auth-form">
    <input type="hidden" name="role" value="<?= htmlspecialchars($role ?? ($_GET['role'] ?? 'jobseeker')) ?>">
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect ?? $_GET['redirect'] ?? '') ?>">

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($loginNotice)): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($loginNotice) ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <div class="password-container">
            <input type="password" id="password" name="password" required>
            <button type="button" class="toggle-password" id="togglePassword" title="Show/hide password">
                <span class="eye-icon">👁️</span>
            </button>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Login</button>
    </div>
</form>
