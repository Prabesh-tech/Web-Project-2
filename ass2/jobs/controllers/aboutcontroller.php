<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

$pageTitle = 'About Us - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
];

ob_start();
require_once __DIR__ . '/../views/about.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
