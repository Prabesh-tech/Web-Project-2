<?php
/**
 * Edit Profile Page - Controller
 * Handles user profile editing
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/usercontroller.php';

// Require login
if (empty($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

try {
    $error = '';
    $success = false;

    $userController = new UserController($pdo);
    $userId = $_SESSION['user']['id'];

    $user = $userController->getUserById($userId);
    if (!$user) {
        throw new Exception("User not found");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $currentPassword = trim($_POST['currentPassword'] ?? '');
        $newPassword = trim($_POST['newPassword'] ?? '');
        $confirmPassword = trim($_POST['confirmPassword'] ?? '');

        try {
            // Update email
            $userController->updateUser($userId, $email);

            // Update password if provided
            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    throw new Exception("Passwords do not match");
                }
                $userController->updatePassword($userId, $newPassword);
            }

            $success = true;
            $user = $userController->getUserById($userId);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    $pageTitle = 'Edit Profile - Prabesh Job';
    $formTitle = 'Edit Profile';

    ob_start();
    require_once 'editProfile.html.php';
    $content = ob_get_clean();

    require_once __DIR__ . '/../layouts/layout-form.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
