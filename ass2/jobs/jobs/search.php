<?php
/**
 * Search Jobs Page - Controller
 * Displays search results for jobs
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';
require_once __DIR__ . '/../includes/categorycontroller.php';

try {
    $jobController = new JobController($pdo);
    $categoryController = new CategoryController($pdo);

    // Get search query
    $searchQuery = trim($_GET['q'] ?? '');
    $categoryId = intval($_GET['category'] ?? 0);
    $page = intval($_GET['page'] ?? 1);
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    // Search jobs
    if (!empty($searchQuery)) {
        $searchResults = $jobController->searchJobs($searchQuery);
    } else {
        $searchResults = $jobController->getAllJobs();
    }

    // Filter by category if specified
    if ($categoryId > 0) {
        $searchResults = array_filter($searchResults, fn($j) => $j['categoryId'] == $categoryId);
    }

    // Pagination
    $totalResults = count($searchResults);
    $totalPages = ceil($totalResults / $perPage);
    $jobs = array_slice($searchResults, $offset, $perPage);

    // Get categories for filter
    $categories = $categoryController->getAllCategories();

    // Get category names for results
    foreach ($jobs as &$job) {
        if ($job['categoryId']) {
            $category = $categoryController->getCategoryById($job['categoryId']);
            $job['categoryName'] = $category['name'] ?? 'Other';
        } else {
            $job['categoryName'] = 'Other';
        }
    }

    $pageTitle = !empty($searchQuery) ? 'Search Results: ' . htmlspecialchars($searchQuery) : 'All Jobs';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Search' => '#',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once 'searchJobs.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
