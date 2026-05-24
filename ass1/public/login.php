<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                // ✅ Determine role from database
                $role = match ((int)$user['isAdmin']) {
                    2 => 'Super Admin',
                    1 => 'Admin',
                    default => 'User',
                };

                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'isAdmin' => $user['isAdmin'],
                    'role' => $role
                ];

                // Redirect all users (including Admins) to the site homepage first
                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <h1>Login</h1>
        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" class="auth-form">
            <div class="form-row">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-row password-row">
                <label for="passwordInput">Password</label>
                <div class="password-field">
                    <input id="passwordInput" type="password" name="password" required>
                    <button type="button" id="togglePassword" class="password-toggle" aria-label="Show password">👁️</button>
                </div>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
        <p class="auth-switch">Don’t have an account? <a href="register.php">Register</a></p>
    </div>
</div>
    <script>
        (function(){
            const pwInput = document.getElementById('passwordInput');
            const toggle = document.getElementById('togglePassword');
            if (!pwInput || !toggle) return;
            toggle.addEventListener('click', function(){
                if (pwInput.type === 'password') {
                    pwInput.type = 'text';
                    toggle.setAttribute('aria-label', 'Hide password');
                    toggle.textContent = '🙈';
                } else {
                    pwInput.type = 'password';
                    toggle.setAttribute('aria-label', 'Show password');
                    toggle.textContent = '👁️';
                }
            });
        })();
    </script>
</body>
</html>
