<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1,2], true)) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);
header('Location: adminCategories.php');
exit;
?>
