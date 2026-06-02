<?php
/**
 * User Profile Page - Controller
 * Displays user profile and statistics
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

try {
    $userController = new UserController($pdo);

    $userId = intval($_GET['id'] ?? 0);
    if ($userId <= 0 && empty($_SESSION['user'])) {
        header('Location: ../login.php');
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

    $pageTitle = htmlspecialchars($user['username']) . ' - Profile';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Profile' => '#',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/profile.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
