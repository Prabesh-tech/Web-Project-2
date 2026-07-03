<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require_once __DIR__ . '/includes/jobcontroller.php';

// Require user to be logged in (allow jobseekers as well)
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$categories = [];

// Get categories
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY CASE WHEN name IN ('Sales & Marketing','Sales/Business Development','Sales','Information Technology','IT – Programming & Development','Human Resource') THEN 0 ELSE 1 END, name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Could not load categories. Please try again.';
}

// Handle form submission
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
        try {
            $jobController = new JobController($pdo);
            $companyId = null; // You can modify this to use user's company if needed
            $postedBy = intval($_SESSION['user']['id'] ?? 0);

            $jobId = $jobController->createJob(
                $title,
                $description,
                $categoryId,
                $salary,
                $companyId,
                $location,
                $postedBy,
                $jobType,
                $closingDate
            );

            $success = 'Job posted successfully! <a href="job.php?id=' . intval($jobId) . '">View your job</a>';
            
            // Clear form
            $_POST = [];
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$formTitle = 'Post a New Job';
$formDescription = 'Fill in the details below to post a new job listing.';
$formFooter = '';
$includeHeader = true;

ob_start();
require __DIR__ . '/views/addJob.html.php';
$content = ob_get_clean();
require __DIR__ . '/layouts/layout-main.php';

