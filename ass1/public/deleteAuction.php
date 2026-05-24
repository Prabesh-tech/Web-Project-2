<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

// Admin check: allow admin (1) or super admin (2) or role 'Super Admin'
$isAdmin = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
           (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
if (!$isAdmin) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manageAuctions.php?msg=error');
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: manageAuctions.php?msg=error');
    exit;
}

try {
    // Remove image file if present
    $stmt = $pdo->prepare('SELECT image FROM auction WHERE id = ?');
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    if ($img) {
        $path = __DIR__ . '/images/auctions/' . $img;
        if (file_exists($path)) {
            @unlink($path);
        }
    }

    $stmt = $pdo->prepare('DELETE FROM auction WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manageAuctions.php?msg=deleted');
    exit;
} catch (PDOException $e) {
    header('Location: manageAuctions.php?msg=error');
    exit;
}
