<?php
/**
 * User Profile Page - Controller
 * Displays user profile and statistics
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

try {
    $error = '';
    $success = false;
    $userController = new UserController($pdo);

    $userId = intval($_GET['id'] ?? 0);
    if ($userId <= 0 && empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    if ($userId <= 0) {
        $userId = $_SESSION['user']['id'];
    }

    $user = $userController->getUserById($userId);
    if (!$user) {
        throw new Exception("User not found");
    }

    $isOwnProfile = isset($_SESSION['user']) && $_SESSION['user']['id'] == $userId;
    $profileCompleteness = 60;

    $statusFilter = strtolower(trim($_GET['status'] ?? 'all'));
    $statusMap = [
        'pending' => 'Applied',
        'shortlisted' => 'Shortlisted',
        'rejected' => 'Rejected',
        'accepted' => 'Accepted',
        'all' => null,
    ];
    $allowedStatuses = [
        'all' => 'All',
        'pending' => 'Pending',
        'shortlisted' => 'Shortlisted',
        'rejected' => 'Rejected',
        'accepted' => 'Accepted',
    ];
    $statusParam = array_key_exists($statusFilter, $statusMap) ? $statusMap[$statusFilter] : null;
    $activeStatus = array_key_exists($statusFilter, $allowedStatuses) ? $statusFilter : 'all';
    $applications = $userController->getUserApplications($userId, $statusParam);

    $viewApplicationId = intval($_GET['viewApplicationId'] ?? 0);
    $viewApplication = null;
    if ($viewApplicationId > 0) {
        $jobController = new JobController($pdo);
        $viewApplication = $jobController->getApplicationById($viewApplicationId);
        if (empty($viewApplication) || intval($viewApplication['userId'] ?? 0) !== $userId) {
            $viewApplication = null;
        }
    }

    $pageTitle = htmlspecialchars($user['username']) . ' - Profile';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Profile' => '#',
    ];

    ob_start();
    require_once __DIR__ . '/../views/profile.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-main.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
