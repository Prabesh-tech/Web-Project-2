<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

$categoryId = intval($_GET['id'] ?? 0);
$jobs = [];
$error = '';
$categoryName = 'Job Category';

try {
    if ($categoryId <= 0) {
        throw new Exception('Invalid category selected.');
    }

    $stmt = $pdo->prepare('SELECT name FROM categories WHERE id = ?');
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        throw new Exception('Category not found.');
    }

    $categoryName = $category['name'];
    $jobController = new JobController($pdo);
    $jobs = $jobController->getJobsByCategory($categoryId);
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = htmlspecialchars($categoryName) . ' Jobs - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
    $categoryName => '#',
];

ob_start();
require __DIR__ . '/../views/category.html.php';
$content = ob_get_clean();
require __DIR__ . '/../layouts/layout-main.php';
