<?php
/**
 * Edit Job Page - Controller
 * Handles form for editing existing jobs
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';
require_once __DIR__ . '/../includes/categorycontroller.php';

// Check authentication
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1, 2], true)) {
    header('Location: ../login.php');
    exit;
}

try {
    $error = '';
    $success = false;

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

    $categories = $categoryController->getAllCategories();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = intval($_POST['categoryId'] ?? 0);
        $salary = trim($_POST['salary'] ?? '');
        $companyName = trim($_POST['companyName'] ?? '');
        $location = trim($_POST['location'] ?? '');

        try {
            $jobController->updateJob($jobId, $title, $description, $categoryId, $salary, $companyName, $location);
            $success = true;
            $job = $jobController->getJobById($jobId);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    $pageTitle = 'Edit Job - Prabesh Job';
    $formTitle = 'Edit Job';
    
    ob_start();
    require_once 'editJob.html.php';
    $content = ob_get_clean();

    require_once __DIR__ . '/../layouts/layout-form.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
