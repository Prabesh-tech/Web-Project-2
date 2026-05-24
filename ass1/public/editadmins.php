<?php
require_once __DIR__ . '/includes/DbConnection.php';
require 'includes/header.php';

if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['isAdmin']) || !in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) {
    die("Access denied (Admin only)");
}

//  GET USER ID
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = (int) $_GET['id'];

// FETCH USER
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

// UPDATE ADMIN STATUS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $isAdmin = isset($_POST['isAdmin']) ? 1 : 0;

    // Prevent admin from removing own admin rights (optional safety)
    if ($id == $_SESSION['user']['id'] && $isAdmin == 0) {
        die("You cannot remove your own admin rights");
    }

    $update = $pdo->prepare("UPDATE users SET isAdmin = ? WHERE id = ?");
    $update->execute([$isAdmin, $id]);

    header("Location: admin.php?updated=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin</title>
</head>
<body>

<h2>Edit User Admin Rights</h2>

<a href="admin.php">Back</a>

<hr>

<p><b>Name:</b> <?= htmlspecialchars($user['name']) ?></p>
<p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>

<form method="POST">

    <label>
        <input type="checkbox" name="isAdmin" 
        <?= $user['isAdmin'] == 1 ? 'checked' : '' ?>>
        Make Admin
    </label>

    <br><br>

    <button type="submit">Update</button>

</form>

</body>
</html>