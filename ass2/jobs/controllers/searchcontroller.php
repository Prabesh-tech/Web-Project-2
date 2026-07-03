<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

$query = trim($_GET['q'] ?? '');
$jobs = [];
$error = '';

try {
    $jobController = new JobController($pdo);
    if ($query !== '') {
        $jobs = $jobController->searchJobs($query, 50);
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = 'Search Jobs - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
];

ob_start();
require __DIR__ . '/../views/search.html.php';
$content = ob_get_clean();
require __DIR__ . '/../layouts/layout-main.php';
