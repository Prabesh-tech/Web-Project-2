<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

$pageTitle = 'Career Advice - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
    'Career Advice' => '#',
];

ob_start();
require_once __DIR__ . '/../views/careers.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
