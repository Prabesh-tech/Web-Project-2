<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/DbConnection.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$baseDirectory = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$homeUrl = $baseDirectory === '' ? '/index.php' : $baseDirectory . '/index.php';

/* FETCH CATEGORIES */
$categories = [];
try {
    if (isset($pdo)) {
            $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY CASE WHEN name IN ('Sales & Marketing','Sales/Business Development','Sales','Information Technology','IT – Programming & Development','Human Resource') THEN 0 ELSE 1 END, name ASC");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $categories = [];
}

/* NAV ITEMS */
$navItems = [
    'Job Categories' => '#',
    'Services' => 'services.php',
    'Blogs' => '#',
];

/* PREPARE DATA FOR HEADER VIEW */

// Filter navigation items (exclude dropdowns already shown)
$navItemsFiltered = [];
foreach ($navItems as $label => $path) {
    if (!in_array($label, ['Job Categories', 'Services', 'Blogs'], true)) {
        $navItemsFiltered[$label] = $path;
    }
}

// Check if user is authenticated and determine role-specific access
$isUserLoggedIn = isset($_SESSION['user']);
$userName = $isUserLoggedIn ? $_SESSION['user']['username'] : '';
$userRole = $isUserLoggedIn && isset($_SESSION['user']['role']) ? intval($_SESSION['user']['role']) : null;
$isEmployerUser = $isUserLoggedIn && $userRole === 1;
$isAdminUser = $isUserLoggedIn && in_array($userRole, [2, 3], true);
$isSuperAdmin = $isUserLoggedIn && $userRole === 3;
$isAdminOrEmployerUser = $isEmployerUser || $isAdminUser;

// Categories list HTML
$categoriesHTML = '';
if (!empty($categories)) {
    foreach ($categories as $category) {
        $categoryId = intval($category['id']);
        $categoryName = htmlspecialchars($category['name']);
        $categoriesHTML .= sprintf('<a href="category.php?id=%d">%s</a>', $categoryId, $categoryName);
    }
} else {
    $categoriesHTML = '<span class="nav-dropdown-empty">No categories available</span>';
}

// Navigation items HTML
$navItemsHTML = '';
foreach ($navItemsFiltered as $label => $path) {
    $safeLabel = htmlspecialchars($label);
    $safePath = htmlspecialchars($path);
    $navItemsHTML .= sprintf('<a href="%s" class="nav-link">%s</a>', $safePath, $safeLabel);
}

// Button HTML (Add Job plus role-specific dropdown actions)
$actionButtonHTML = '';
if ($isUserLoggedIn) {
    $userId = intval($_SESSION['user']['id']);
    $dropdownLinksHTML = '';
    if ($isAdminUser) {
        $dropdownLinksHTML = '
                <a href="admin.php" class="dropdown-item">Admin Dashboard</a>
                <a href="adminCategories.php" class="dropdown-item">Manage Categories</a>
                <a href="manageUsers.php" class="dropdown-item">Manage Users</a>
                <a href="editProfile.php" class="dropdown-item">Edit Profile</a>';
        // Only Super Admin (role 3) can add new Admin accounts
        if ($isSuperAdmin) {
            $dropdownLinksHTML .= '\n                <a href="register.php?role=admin" class="dropdown-item">Add Admin</a>';
        }
    } elseif ($isEmployerUser) {
        $dropdownLinksHTML = '
                <a href="editProfile.php" class="dropdown-item">Edit Profile</a>
                <a href="profile.php?id=' . $userId . '" class="dropdown-item">My Profile</a>';
    }

    // Profile Info button for all users
    $profileInfoItem = sprintf('<a href="profile.php?id=%d" class="dropdown-item">Profile Info</a>', $userId);

    $actionButtonHTML = '<div class="add-dropdown-container">
            <button type="button" class="btn-addauction dropdown-btn" aria-haspopup="true" aria-expanded="false" onclick="toggleAddDropdown()">More</button>
            <div id="addDropdownMenu" class="add-dropdown-menu" role="menu">
                <a href="addJob.php" class="dropdown-item" role="menuitem">Add Job</a>
                <a href="viewJobs.php" class="dropdown-item" role="menuitem">Job List</a>' .
                $profileInfoItem .
                $dropdownLinksHTML .
            '</div>
        </div>';
}

// User section HTML
$userSectionHTML = '';
if ($isUserLoggedIn) {
    $safeUserName = htmlspecialchars($userName);
    $userSectionHTML = sprintf('<span class="username">Hi, %s</span>', $safeUserName);
    $userSectionHTML .= '<a href="logout.php" class="btn-logout">Logout</a>';
} else {
    $userSectionHTML = '<a href="login.php" class="user-icon" title="Login">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="8" r="4"/>
        <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
    </svg>
</a>
<a href="login.php" class="btn-hero">Login</a>
<a href="register.php" class="btn-hero outline">Register</a>';
}
?>

<!-- RENDER HEADER VIEW -->
<?php require_once __DIR__ . '/../views/header.html.php'; ?>
