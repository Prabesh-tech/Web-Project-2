<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

$error = '';
$success = '';
$name = '';
$email = '';
$subject = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = 'Name, email, and message are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO enquiries (name, email, subject, message, status, createdAt, updatedAt) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$name, $email, $subject, $message, 'Unread', $now, $now]);
            $success = 'Your message was sent successfully. Our team will contact you soon.';
            $name = $email = $subject = $message = '';
        } catch (Exception $e) {
            $error = 'Could not send your message at this time. Please try again later.';
        }
    }
}

$pageTitle = 'Contact Us - Prabesh Job';
$breadcrumbs = [
    'Home' => 'index.php',
    'Contact Us' => '#',
];

ob_start();
require_once __DIR__ . '/../views/contact.html.php';
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/layout-main.php';
