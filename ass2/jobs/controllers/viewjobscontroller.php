<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

$jobs = [];
$error = '';
$categories = [];
$searchQuery = trim($_GET['q'] ?? '');
$categoryId = intval($_GET['category'] ?? 0);
$createdAfter = trim($_GET['createdAfter'] ?? '');
$createdBefore = trim($_GET['createdBefore'] ?? '');

try {
    $jobController = new JobController($pdo);
    $includeArchived = isset($_SESSION['user']) && in_array(intval($_SESSION['user']['role'] ?? 0), [2, 3], true);
    $jobs = $jobController->getAllJobs(
        $categoryId > 0 ? $categoryId : null,
        null,
        0,
        $includeArchived,
        $searchQuery !== '' ? $searchQuery : null,
        $createdAfter !== '' ? $createdAfter : null,
        $createdBefore !== '' ? $createdBefore : null
    );

    // If the current user is an employer (role 1), only show jobs they posted.
    $currentRole = intval($_SESSION['user']['role'] ?? -1);
    $currentUserId = intval($_SESSION['user']['id'] ?? 0);
    if ($currentRole === 1) {
        $jobs = array_values(array_filter($jobs, function($j) use ($currentUserId) {
            return intval($j['postedBy'] ?? 0) === $currentUserId;
        }));
    }

    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY CASE WHEN name IN ('Sales & Marketing','Sales/Business Development','Sales','Information Technology','IT – Programming & Development','Human Resource') THEN 0 ELSE 1 END, name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = 'Prabesh Job - All Jobs';
$breadcrumbs = [
    'Home' => 'index.php',
    'All Jobs' => '#',
];

ob_start();
require __DIR__ . '/../views/viewJobs.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
