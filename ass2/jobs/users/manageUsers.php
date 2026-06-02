<?php
/**
 * Manage Users Page (Admin) - Controller
 * Lists and manages all users
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

// Check if user is admin
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1, 2], true)) {
    header('Location: ../login.php');
    exit;
}

try {
    $error = '';
    $success = false;

    $userController = new UserController($pdo);

    // Handle delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
        $userId = intval($_POST['id'] ?? 0);
        try {
            $userController->deleteUser($userId);
            $success = true;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Search or get all
    $search = trim($_GET['search'] ?? '');
    $page = intval($_GET['page'] ?? 1);
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    if (!empty($search)) {
        $users = $userController->searchUsers($search);
    } else {
        $users = $userController->getAllUsers($perPage, $offset);
    }

    $totalUsers = $userController->getUserCount();
    $totalPages = ceil($totalUsers / $perPage);
    $currentPage = $page;

    if (isset($_GET['status'])) {
        if ($_GET['status'] === 'added' || $_GET['status'] === 'deleted') {
            $success = true;
        }
    }

    $pageTitle = 'Manage Users - Prabesh Job';
    $breadcrumbs = [
        'Home' => '../index.php',
        'Admin' => '../admin.php',
        'Users' => 'manageUsers.php',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once 'manageUsers.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
