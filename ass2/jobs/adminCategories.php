<?php
/**
 * Admin Manage Categories Page - Controller
 * Handles listing, adding, editing, and deleting job categories
 */

session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require_once __DIR__ . '/includes/categorycontroller.php';

// Check admin access
if (!isset($_SESSION['user']['id']) || 
   !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1, 2], true)) {
    header("Location: login.php");
    exit;
}

try {
    $error = '';
    $success = false;

    // Initialize controller
    $categoryController = new CategoryController($pdo);

    // Handle Delete Category
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        try {
            $categoryController->deleteCategory($id);
            $success = true;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // Fetch all categories with job counts
    $categories = $categoryController->getAllCategories();
    
    // Add job counts to categories
    foreach ($categories as &$cat) {
        $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM jobs WHERE categoryId = ?');
        $stmt->execute([$cat['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $cat['job_count'] = $result['count'] ?? 0;
    }

    // Get total category count
    $categoryCount = $categoryController->getCategoryCount();

    // Check for success message from URL
    if (isset($_GET['status']) && $_GET['status'] === 'added') {
        $success = true;
    }

    // Set page data
    $pageTitle = 'Manage Categories - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Admin' => 'admin.php',
        'Categories' => 'adminCategories.php',
    ];

    // Include header
    require_once 'includes/header.php';

    // Include view
    require_once 'adminCategories.html.php';

    // Include footer
    require_once 'includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}

