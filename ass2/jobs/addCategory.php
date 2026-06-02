<?php
/**
 * Add Category Page - Controller
 * Handles form for adding new job categories
 */

session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require_once __DIR__ . '/includes/categorycontroller.php';

// Check admin access
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1, 2], true)) {
    header('Location: login.php');
    exit;
}

try {
    $error = '';
    $success = false;

    // Initialize controller
    $categoryController = new CategoryController($pdo);

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');

        try {
            $categoryController->createCategory($name, $description, $image);
            header('Location: adminCategories.php?status=added');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Set page title
    $pageTitle = 'Add New Category - Prabesh Job';

    // Include layout with view
    include 'layouts/layout-form.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

