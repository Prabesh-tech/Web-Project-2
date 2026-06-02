<?php
/**
 * Delete Job Handler
 * Handles job deletion via POST
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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $jobId = intval($_POST['id'] ?? 0);
        $jobController = new JobController($pdo);
        $jobController->deleteJob($jobId);
        header('Location: manageJobs.php?status=deleted');
        exit;
    }

    header('Location: manageJobs.php?status=error');

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
