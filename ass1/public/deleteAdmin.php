<?php
require 'db.php';
session_start();

//  ADMIN ONLY CHECK - allow isAdmin 1 or 2
if (!isset($_SESSION['user']['id']) || !isset($_SESSION['user']['isAdmin']) || !in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) {
    die("Access denied (Admin only)");
}

// GET USER ID
if (!isset($_GET['id'])) {
    die("Invalid request");
}

$id = (int) $_GET['id'];

//  Prevent admin deleting themselves (optional safety)
if ($id == $_SESSION['user']['id']) {
    die("You cannot delete your own account");
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: admin.php?deleted=1");
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>