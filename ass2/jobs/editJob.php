<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require_once __DIR__ . '/includes/jobcontroller.php';

if (!isset($_SESSION['user']) || !in_array(intval($_SESSION['user']['role']), [1, 2, 3], true)) {
    header('Location: login.php?role=employer');
    exit;
}

$jobId = intval($_GET['id'] ?? 0);
$error = '';
$success = '';
$categories = [];
$job = null;

try {
    $jobController = new JobController($pdo);
    $job = $jobController->getJobById($jobId);

    if (!$job) {
        throw new Exception('Job not found');
    }

    $currentUserId = intval($_SESSION['user']['id'] ?? 0);
    if (intval($job['postedBy'] ?? 0) !== $currentUserId) {
        throw new Exception('Only the user who posted this job can edit it.');
    }

    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY CASE WHEN name IN ('Sales & Marketing','Sales/Business Development','Sales','Information Technology','IT – Programming & Development','Human Resource') THEN 0 ELSE 1 END, name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = intval($_POST['categoryId'] ?? 0);
        $salary = trim($_POST['salary'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $jobType = trim($_POST['jobType'] ?? '');
        $closingDate = trim($_POST['closingDate'] ?? '');

        if ($title === '' || $description === '' || $categoryId <= 0) {
            $error = 'Title, description, and category are required fields.';
        } else {
            $jobController->updateJob($jobId, $title, $description, $categoryId, $salary, null, $location, $jobType, $closingDate);
            $success = 'Job updated successfully.';
            $job = $jobController->getJobById($jobId);
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

$pageTitle = 'Edit Job - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
    'Edit Job' => '#',
];

ob_start();
require __DIR__ . '/views/addJob.html.php';
$content = ob_get_clean();
require __DIR__ . '/layouts/layout-main.php';
