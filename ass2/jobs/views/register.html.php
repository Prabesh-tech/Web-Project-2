<form method="POST" class="auth-form">
    <input type="hidden" name="role" value="<?= htmlspecialchars($role ?? ($_GET['role'] ?? 'jobseeker')) ?>">
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="email">Email address</label>
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

    <?php if (($role ?? '') === 'admin'): ?>
        <input type="hidden" name="admin_role" value="admin">
        <div class="form-actions">
            <button type="submit" class="btn-submit">Create Admin</button>
        </div>
    <?php else: ?>
        <div class="form-actions">
            <button type="submit" class="btn-submit">Register</button>
        </div>
    <?php endif; ?>
</form>
