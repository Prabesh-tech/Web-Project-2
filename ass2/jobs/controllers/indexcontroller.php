<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

$jobs = [];
$error = '';

try {
    $jobController = new JobController($pdo);
    $jobs = $jobController->getAllJobs(null, 12);
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = 'Prabesh Job - Home';
$breadcrumbs = ['Home' => '#'];

ob_start();
require __DIR__ . '/../views/index.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
