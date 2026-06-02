<?php
/**
 * Job Detail Page - Controller
 * Displays single job with application form
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';
require_once __DIR__ . '/../includes/categorycontroller.php';

try {
    $jobController = new JobController($pdo);
    $categoryController = new CategoryController($pdo);

    $jobId = intval($_GET['id'] ?? 0);
    if ($jobId <= 0) {
        throw new Exception("Invalid job ID");
    }

    $job = $jobController->getJobById($jobId);
    if (!$job) {
        throw new Exception("Job not found");
    }

    $category = $categoryController->getCategoryById($job['categoryId']);
    $categoryName = $category['name'] ?? 'Other';

    $relatedJobs = $jobController->getJobsByCategory($job['categoryId']);
    $relatedJobs = array_filter($relatedJobs, fn($j) => $j['id'] != $jobId);
    $relatedJobs = array_slice($relatedJobs, 0, 3);

    $pageTitle = htmlspecialchars($job['title']) . ' - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Jobs' => 'search.php',
        'Job Detail' => '#',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/job.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
