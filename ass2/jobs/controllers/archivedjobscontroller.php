<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['role'] ?? 0), [1, 2, 3], true)) {
    header('Location: login.php?role=admin');
    exit;
}

$error = '';
try {
    $jobController = new JobController($pdo);
    $jobs = $jobController->getAllJobs(null, null, 0, true);
    $archivedJobs = array_filter($jobs, fn($job) => intval($job['isArchived'] ?? 0) === 1);

    $pageTitle = 'Archived Jobs - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Archived Jobs' => 'archivedJobs.php',
    ];

    ob_start();
    require_once __DIR__ . '/../views/archivedJobs.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-admin.php';

} catch (Exception $e) {
    $error = $e->getMessage();
    ob_start();
    require_once __DIR__ . '/../views/archivedJobs.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-admin.php';
}
