<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

function requireLogin() {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function redirectIfLoggedIn() {
    if (!empty($_SESSION['user'])) {
        header('Location: index.php');
        exit;
    }
}

function getUserRole() {
    if (!empty($_SESSION['user']['role'])) {
        return $_SESSION['user']['role'];
    }

    if (isset($_SESSION['user']['isAdmin'])) {
        switch (intval($_SESSION['user']['isAdmin'])) {
            case 2:
                return 'Super Admin';
            case 1:
                return 'Admin';
            default:
                return 'User';
        }
    }

    return null;
}

function isAdminUser() {
    return (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
           (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
}

function isUserAllowedToPostJob() {
    $role = getUserRole();
    return in_array($role, ['User', 'Admin', 'Super Admin'], true);
}

function requireAdmin() {
    if (empty($_SESSION['user']) || !isAdminUser()) {
        header('Location: login.php');
        exit;
    }
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function logoutUser() {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
