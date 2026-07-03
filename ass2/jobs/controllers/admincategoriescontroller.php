<?php
/**
 * Admin Categories Controller
 * Handles category management (add, edit, delete)
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

// Check if user is admin
if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['role'] ?? 0), [1, 2, 3], true)) {
    header('Location: login.php?role=admin');
    exit;
}

try {
    $error = '';
    $success = false;
    $action = $_GET['action'] ?? 'list';

    // Handle add category
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $categoryName = trim($_POST['name'] ?? '');
            $categoryDescription = trim($_POST['description'] ?? '');

            if ($categoryName === '') {
                $error = 'Category name is required.';
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                    $stmt->execute([$categoryName, $categoryDescription]);
                    $success = true;
                    $_GET['status'] = 'added';
                } catch (PDOException $e) {
                    $error = 'Could not add category. It may already exist.';
                }
            }
        } elseif ($_POST['action'] === 'edit') {
            $categoryId = intval($_POST['id'] ?? 0);
            $categoryName = trim($_POST['name'] ?? '');
            $categoryDescription = trim($_POST['description'] ?? '');

            if ($categoryName === '' || $categoryId <= 0) {
                $error = 'Category name is required.';
            } else {
                try {
                    $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                    $stmt->execute([$categoryName, $categoryDescription, $categoryId]);
                    $success = true;
                    $_GET['status'] = 'updated';
                } catch (PDOException $e) {
                    $error = 'Could not update category.';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $categoryId = intval($_POST['id'] ?? 0);

            if ($categoryId <= 0) {
                $error = 'Invalid category ID.';
            } else {
                try {
                    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                    $stmt->execute([$categoryId]);
                    $success = true;
                    $_GET['status'] = 'deleted';
                } catch (PDOException $e) {
                    $error = 'Could not delete category. It may have associated jobs.';
                }
            }
        }
    }

    // Get categories
    $categories = [];
    $stmt = $pdo->query("SELECT id, name, description FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get single category for editing
    $editCategory = null;
    if ($action === 'edit' && isset($_GET['id'])) {
        $categoryId = intval($_GET['id']);
        $stmt = $pdo->prepare("SELECT id, name, description FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $pageTitle = 'Manage Categories - Prabesh Job';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Dashboard' => 'admin.php',
        'Categories' => 'adminCategories.php',
    ];

    ob_start();
    require_once __DIR__ . '/../views/adminCategories.html.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../layouts/layout-main.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
