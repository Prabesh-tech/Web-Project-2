<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$loginNotice = '';

if (!empty($_GET['redirect']) && str_contains($_GET['redirect'], 'apply.php')) {
    $loginNotice = 'Please login to continue your application.';
}

// Determine selected role (from query string or POST)
$role = $_GET['role'] ?? ($_POST['role'] ?? 'jobseeker');
$redirect = $_GET['redirect'] ?? ($_POST['redirect'] ?? '');

function isSafeRedirect(string $url): bool {
    if (empty($url)) {
        return false;
    }

    $parsed = parse_url($url);
    if ($parsed === false) {
        return false;
    }

    return empty($parsed['scheme']) && empty($parsed['host']) && strpos($url, '//') === false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
        $redirect = $_POST['redirect'] ?? $redirect;

        if ($email === '' || $password === '') {
            $error = 'Email and password are required.';
        } else {
            try {
                $userController = new UserController($pdo);
                $user = $userController->authenticateWithEmail($email, $password);

                if ($user) {
                    $actualRole = intval($user['role']);
                    $loginType = $actualRole === 1 ? 'employer' : (($actualRole === 2 || $actualRole === 3) ? 'admin' : 'jobseeker');

                    $_SESSION['user'] = $user;
                    $_SESSION['user']['loginType'] = $loginType;

                    if (isSafeRedirect($redirect)) {
                        header('Location: ' . $redirect);
                    } elseif (in_array($actualRole, [2, 3], true)) {
                        header('Location: admin.php');
                    } else {
                        header('Location: profile.php?id=' . intval($user['id']));
                    }
                    exit;
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }

$pageTitle = 'Login - Prabesh Job';
$formTitle = 'Login';
$formDescription = 'Sign in to your account using your registered email and password.';
$formFooter = '<p>Don\'t have an account? <a href="register.php">Register here</a>.</p>';
$includeHeader = false;
$pageClass = 'login-page';

ob_start();
require __DIR__ . '/../views/login.html.php';
$content = ob_get_clean();
require __DIR__ . '/../layouts/layout-form.php';
