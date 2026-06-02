<?php
/**
 * Add Job Page - Controller
 * Handles form for posting new jobs
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

    $categories = $categoryController->getAllCategories();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = intval($_POST['categoryId'] ?? 0);
        $salary = trim($_POST['salary'] ?? '');
        $companyName = trim($_POST['companyName'] ?? '');
        $location = trim($_POST['location'] ?? '');

        try {
            $jobController->createJob($title, $description, $categoryId, $salary, $companyName, $location);
            header('Location: manageJobs.php?status=added');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    $pageTitle = 'Post New Job - Prabesh Job';
    $formTitle = 'Post New Job';
    $formDescription = 'Fill in the details to post a new job opening';
    
    ob_start();
    require_once 'addJob.html.php';
    $content = ob_get_clean();

    require_once __DIR__ . '/../layouts/layout-form.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
