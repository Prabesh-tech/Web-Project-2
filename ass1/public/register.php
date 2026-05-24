<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

/* ---------------- REDIRECT IF LOGGED IN ---------------- */
if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = false;

$username = '';
$email = '';

/* ---------------- FORM HANDLING ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    /* ---------------- VALIDATION ---------------- */
    if ($username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {

        // ✅ Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            /* ---------------- CHECK EMAIL EXISTS ---------------- */
            $check = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $check->execute([$email]);

            if ($check->rowCount() > 0) {
                $error = 'Email already registered.';
            } else {
                /* ---------------- GET NEXT ID MANUALLY ---------------- */
                $stmtMax = $pdo->query("SELECT MAX(id) AS max_id FROM users");
                $row = $stmtMax->fetch(PDO::FETCH_ASSOC);
                $nextId = ($row['max_id'] ?? 0) + 1;

                /* ---------------- INSERT USER ---------------- */
                $stmt = $pdo->prepare(
                    'INSERT INTO users (id, username, email, password, isAdmin)
                     VALUES (?, ?, ?, ?, ?)'
                );

                $stmt->execute([
                    $nextId,          // ✅ manually assigned ID
                    $username,
                    $email,
                    $hashedPassword,
                    0                 // ✅ normal user
                ]);

                $success = true;

                // clear form
                $username = '';
                $email = '';
            }
        } catch (PDOException $e) {
            // DEBUG MODE (remove in production)
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
<title>Register</title>
<link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <h1>Create Account</h1>

        <?php if ($success): ?>
            <div class="auth-success">
                Registration successful! <a href="login.php">Login here</a>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">

            <div class="form-row">
                <label>Username</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <div class="form-row">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="form-row">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Register</button>

        </form>

        <p class="auth-switch">
            Already have an account? <a href="login.php">Login</a>
        </p>

    </div>
</div>

</body>
</html>
