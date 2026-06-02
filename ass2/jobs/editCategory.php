<?php
/**
 * Edit Category Page - Controller
 * Handles form for editing existing job categories
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

    // Get category ID
    $categoryId = intval($_GET['id'] ?? 0);
    if ($categoryId <= 0) {
        throw new Exception("Invalid category ID");
    }

    // Fetch category
    $category = $categoryController->getCategoryById($categoryId);
    if (!$category) {
        throw new Exception("Category not found");
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? '');

        try {
            $categoryController->updateCategory($categoryId, $name, $description, $image);
            $success = true;
            // Refresh category data
            $category = $categoryController->getCategoryById($categoryId);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Set page title
    $pageTitle = 'Edit Category - Prabesh Job';
    $formTitle = 'Edit Category';
    $formDescription = 'Update job category details';
    $content = file_get_contents(__DIR__ . '/editCategory.html.php');

    // Prepare variables for view
    $formTitle = 'Edit ' . htmlspecialchars($category['name']);
    $includeHeader = false;
    $includeFooter = false;

    // Include layout
    ob_start();
    include 'editCategory.html.php';
    $content = ob_get_clean();

    // Set page title
    $pageTitle = 'Edit Category - Prabesh Job';
    $formTitle = 'Edit Category';

    // Use form layout
    include 'layouts/layout-form.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
