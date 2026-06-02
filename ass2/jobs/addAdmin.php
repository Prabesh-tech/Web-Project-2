<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

if (!isset($_SESSION['user']['id']) || $_SESSION['user']['email'] !== 'prabesh@gmail.com') {
    header("Location: login.php");
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$email]);

            if ($check->rowCount() > 0) {
                $error = 'Email already registered.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO users (username, email, password, isAdmin)
                     VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([$name, $email, $hashedPassword, 1]); 
                $success = true;
            }
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}

$username = htmlspecialchars($_SESSION['user']['username']);
$role = $_SESSION['user']['role'] ?? 'User';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Admin</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="admin-page">
    <div class="admin-card">
        <h2>Welcome, <?= $username ?> (<?= $role ?>)</h2>
        <h3>Create Admin (Super Admin Only)</h3>
        <?php if ($success): ?><div class="auth-success">✅ Admin created successfully!</div><?php endif; ?>
        <?php if ($error): ?><div class="auth-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" class="admin-form">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn-admin">Create Admin</button>
        </form>
        <a href="admin.php" class="admin-back">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
