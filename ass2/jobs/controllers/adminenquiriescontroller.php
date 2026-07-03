<?php
/**
 * Admin Enquiries Controller
 * Displays contact enquiries for admin review
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['role'] ?? 0), [2, 3], true)) {
    header('Location: login.php?role=admin');
    exit;
}

$error = '';
$enquiries = [];

try {
    $stmt = $pdo->query('SELECT id, name, email, subject, message, status, createdAt FROM enquiries ORDER BY createdAt DESC');
    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Unable to load enquiries at this time.';
}

$pageTitle = 'Contact Enquiries - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
    'Dashboard' => 'admin.php',
    'Enquiries' => '#',
];

ob_start();
require_once __DIR__ . '/../views/adminEnquiries.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
