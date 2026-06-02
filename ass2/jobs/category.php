<?php
/**
 * Category Detail Page - Controller
 * Handles displaying a single category with its jobs
 */

session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require_once __DIR__ . '/includes/categorycontroller.php';

try {
    // Initialize controller
    $categoryController = new CategoryController($pdo);

    // Get category ID from URL
    $categoryId = intval($_GET['id'] ?? 0);
    
    if ($categoryId <= 0) {
        throw new Exception("Invalid category ID");
    }

    // Fetch category
    $category = $categoryController->getCategoryById($categoryId);
    if (!$category) {
        throw new Exception("Category not found");
    }

    // Get category image path
    $categoryImage = $categoryController->getCategoryImagePath($category);

    // Fetch jobs in this category
    $jobs = $categoryController->getJobsByCategory($categoryId);

    // Set page title
    $pageTitle = htmlspecialchars($category['name']) . ' Jobs - Prabesh Job';

    // Include header
    require_once 'includes/header.php';

    // Include view
    require_once 'category.html.php';

    // Include footer
    require_once 'includes/footer.php';

} catch (Exception $e) {
    session_destroy();
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
