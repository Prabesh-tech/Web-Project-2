<?php
/**
 * Manage Users Page (Admin) - Controller
 * Lists and manages all users
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

// Check if user is admin or super admin
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['role'] ?? 0), [1, 2, 3], true)) {
    header('Location: login.php');
    exit;
}

try {
    $error = '';
    $success = false;
    $successMessage = '';
    $formValues = [
        'username' => '',
        'email' => '',
        'role' => 'jobseeker',
    ];

    $userController = new UserController($pdo);

    // Handle create or delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            $userId = intval($_POST['id'] ?? 0);
            try {
                $userController->deleteUser($userId);
                $success = true;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        } elseif ($_POST['action'] === 'create') {
            $formValues['username'] = trim($_POST['username'] ?? '');
            $formValues['email'] = trim($_POST['email'] ?? '');
            $formValues['role'] = $_POST['role'] ?? 'jobseeker';

            if ($formValues['username'] === '' || $formValues['email'] === '' || empty($_POST['password'])) {
                $error = 'Username, email, and password are required to add a user.';
            } else {
                $roleMap = [
                    'jobseeker' => 0,
                    'employer' => 1,
                    'admin' => 2,
                ];
                $roleFlag = $roleMap[$formValues['role']] ?? 0;

                try {
                    $userController->createUser(
                        $formValues['username'],
                        $formValues['email'],
                        $_POST['password'],
                        $roleFlag
                    );
                    $success = true;
                    $successMessage = 'User created successfully.';
                    $formValues = ['username' => '', 'email' => '', 'role' => 'jobseeker'];
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }
    }

    // Search or get all
    $search = trim($_GET['search'] ?? '');
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    if ($search !== '') {
        $users = $userController->searchUsers($search, $perPage, $offset);
        $totalUsers = $userController->getUserCount($search);
    } else {
        $users = $userController->getAllUsers($perPage, $offset);
        $totalUsers = $userController->getUserCount();
    }

    $totalPages = $totalUsers > 0 ? ceil($totalUsers / $perPage) : 1;
    $currentPage = $page;

    if (isset($_GET['status'])) {
        if ($_GET['status'] === 'added' || $_GET['status'] === 'deleted') {
            $success = true;
        }
    }

    $pageTitle = 'Manage Users - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Users' => 'manageUsers.php',
    ];

    ob_start();
    require_once __DIR__ . '/../views/manageUsers.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-main.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
