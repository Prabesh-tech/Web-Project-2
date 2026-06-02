<?php
/**
 * Manage Jobs Page - Controller (Admin)
 * Lists all jobs with edit/delete options
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

// Check authentication
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1, 2], true)) {
    header('Location: ../login.php');
    exit;
}

try {
    $error = '';
    $success = false;

    $jobController = new JobController($pdo);

    // Handle delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $jobId = intval($_POST['id'] ?? 0);
        try {
            $jobController->deleteJob($jobId);
            $success = true;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Pagination
    $page = intval($_GET['page'] ?? 1);
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    $jobCount = $jobController->getJobCount();
    $totalPages = ceil($jobCount / $perPage);

    $stmt = $pdo->prepare(
        'SELECT j.*, c.name as categoryName FROM jobs j 
         LEFT JOIN categories c ON j.categoryId = c.id 
         ORDER BY j.createdAt DESC LIMIT ? OFFSET ?'
    );
    $stmt->execute([$perPage, $offset]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['status'])) {
        if ($_GET['status'] === 'added' || $_GET['status'] === 'deleted') {
            $success = true;
        }
    }

    $pageTitle = 'Manage Jobs - Prabesh Job';
    $breadcrumbs = [
        'Home' => '../index.php',
        'Admin' => '../admin.php',
        'Jobs' => 'manageJobs.php',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once 'manageJobs.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
