<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['role'] ?? 0), [2, 3], true)) {
    header('Location: login.php?role=admin');
    exit;
}

$currentRole = intval($_SESSION['user']['role'] ?? 0);
$canCreateAdmin = $currentRole === 3;
$error = '';
$successMessage = '';
try {
    $userController = new UserController($pdo);
    $adminUsers = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'delete') {
            $userId = intval($_POST['id'] ?? 0);
            if ($userId > 0 && $userId !== intval($_SESSION['user']['id'] ?? 0)) {
                $userController->deleteUser($userId);
                $successMessage = 'Admin account deleted successfully.';
            } else {
                $error = 'Unable to delete this admin account.';
            }
        } else {
            if (!$canCreateAdmin) {
                $error = 'Only a Super Admin can create new admin accounts.';
            } else {
                $username = trim($_POST['username'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $password = $_POST['password'] ?? '';
                $roleFlag = 1; // Always create regular Admin accounts

                if ($username === '' || $email === '' || $password === '') {
                    $error = 'All fields are required to create an admin account.';
                } else {
                    $userController->createUser($username, $email, $password, $roleFlag);
                    $successMessage = 'Admin account created successfully.';
                }
            }
        }
    }

    $stmt = $pdo->query('SELECT id, username, email, role, createdAt FROM users WHERE role > 0 ORDER BY createdAt DESC');
    $adminUsers = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $pageTitle = 'Manage Admins - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Manage Admins' => 'manageAdmins.php',
    ];

    $showAdminForm = $canCreateAdmin;

    ob_start();
    require_once __DIR__ . '/../views/manageAdmins.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-admin.php';

} catch (Exception $e) {
    $error = $e->getMessage();
    ob_start();
    require_once __DIR__ . '/../views/manageAdmins.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-admin.php';
}
