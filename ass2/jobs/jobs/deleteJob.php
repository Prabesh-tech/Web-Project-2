<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

// Admin check: allow admin (1) or super admin (2) or role 'Super Admin'
$isAdmin = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
           (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
if (!$isAdmin) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manageJobs.php?msg=error');
    exit;
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: manageJobs.php?msg=error');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM jobs WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manageJobs.php?msg=deleted');
    exit;
} catch (PDOException $e) {
    header('Location: manageJobs.php?msg=error');
    exit;
}
