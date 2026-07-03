<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

// Determine selected role (from query string or POST)
$role = $_GET['role'] ?? ($_POST['role'] ?? 'jobseeker');

// Only allow a logged-in superadmin to access the Add Admin page
if ($role === 'admin') {
    if (empty($_SESSION['user']) || intval($_SESSION['user']['role'] ?? 0) !== 3) {
        header('Location: login.php?role=admin');
        exit;
    }
} elseif (isset($_SESSION['user'])) {
    header('Location: profile.php?id=' . intval($_SESSION['user']['id']));
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $userController = new UserController($pdo);

            if ($role === 'admin') {
                // Only one Super Admin exists; new admin accounts are created as normal Admins.
                $roleFlag = 2;
                $userController->createUser($username, $email, $password, $roleFlag);
                $success = 'Admin account created successfully.';
            } else {
                $roleFlag = ($role === 'employer') ? 1 : 0;
                $userController->createUser($username, $email, $password, $roleFlag);
                $user = $userController->getUserByUsername($username);

                if ($user) {
                    $_SESSION['user'] = $user;
                    header('Location: profile.php?id=' . intval($user['id']));
                    exit;
                }

                $error = 'Registration succeeded, but we could not log you in automatically. Please login manually.';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

if ($role === 'admin') {
    $formTitle = 'Add New Admin';
    $formDescription = 'Create a new admin account. Only Super Admin can add admins.';
    $formFooter = '<p><a href="admin.php">Back to Admin Dashboard</a></p>';
    $allowAdminRole = true;
} else {
    $formTitle = 'Create a new account';
    $formDescription = 'Register an account to apply for jobs and manage your profile.';
    $formFooter = '<p>Already have an account? <a href="login.php">Login here</a>.</p>';
    $allowAdminRole = false;
}

$includeHeader = false;
$pageClass = 'register-page';

ob_start();
require __DIR__ . '/../views/register.html.php';
$content = ob_get_clean();
require __DIR__ . '/../layouts/layout-form.php';
