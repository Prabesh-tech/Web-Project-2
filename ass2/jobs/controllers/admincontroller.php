<?php
/**
 * Admin Dashboard Controller
 * Displays admin statistics and controls
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

// Check if user is admin
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['role'] ?? 0), [1, 2, 3], true)) {
    header('Location: login.php?role=admin');
    exit;
}

try {
    $error = '';
    $success = false;

    $userController = new UserController($pdo);
    $jobController = new JobController($pdo);

    // Get statistics
    $stats = [];
    
    // Total users
    $userStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['totalUsers'] = $userStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total jobs
    $jobStmt = $pdo->query("SELECT COUNT(*) as total FROM jobs");
    $stats['totalJobs'] = $jobStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total categories
    $catStmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
    $stats['totalCategories'] = $catStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Total applications
    $appStmt = $pdo->query("SELECT COUNT(*) as total FROM applications");
    $stats['totalApplications'] = $appStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Recent jobs
    $recentJobs = $jobController->getAllJobs(null, 5, 0);

    // Recent users
    $recentUsers = $userController->getAllUsers(5, 0);

    $pageTitle = 'Admin Dashboard - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Dashboard' => 'admin.php',
    ];

    ob_start();
    require_once __DIR__ . '/../views/admin.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-main.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
