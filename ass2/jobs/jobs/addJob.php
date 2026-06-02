<?php
require_once __DIR__ . '/../auth.php';

// Allow authenticated users to create jobs (normal users and admins)
$error = '';
$success = false;
$formAction = 'jobs/addJob.php';

if (empty(currentUser()) || !isUserAllowedToPostJob()) {
    if (isset($_GET['embedded'])) {
        http_response_code(403);
        echo "<div class='auth-error'>You must be a normal user or admin to add jobs.</div>";
        exit;
    }
    header('Location: ../login.php');
    exit;
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$title = '';
$description = '';
$year = '';
$mileage = '';
$currentBid = '';
$endDate = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // keep same behaviour for job listings
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = intval($_POST['categoryId'] ?? 0);
    $year = intval($_POST['year'] ?? 0);
    $mileage = intval($_POST['mileage'] ?? 0);
    $currentBid = floatval($_POST['currentBid'] ?? 0);
    $endDate = trim($_POST['endDate'] ?? '');

    if ($categoryId <= 0) {
        $error = 'Please select a valid category.';
    }

    if (empty($categories)) {
        $error = 'Please create at least one category before adding a job.';
    }

    if ($error === '') {
        $categoryExists = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE id = ?");
        $categoryExists->execute([$categoryId]);
        if ((int)$categoryExists->fetchColumn() === 0) {
            $error = 'Selected category does not exist. Please choose a valid category.';
        }
    }

    if ($endDate !== '') {
        $date = DateTime::createFromFormat('Y-m-d\\TH:i', $endDate);
        if ($date === false) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $endDate);
        }
        if ($date === false) {
            $error = 'Please enter a valid end date and time.';
        } else {
            $endDate = $date->format('Y-m-d H:i:s');
        }
    } else {
        $error = 'An end date is required.';
    }

    if ($error === '') {
        $userId = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
        if ($userId <= 0) {
            $error = 'Unable to determine your user account. Please log in again and try.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    'INSERT INTO jobs (title, description, categoryId, postedBy, salary, jobType, closingDate) VALUES (?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$title, $description, $categoryId, $userId, $currentBid, 'Full Time', $endDate]);
                header('Location: ../index.php');
                exit;
            } catch (PDOException $e) {
                $error = "DB Error: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
// If requested to embed only the form (modal), return form fragment
if (isset($_GET['embedded'])) {
    include __DIR__ . '/../includes/addJob_form.php';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Job</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="admin-page">
    <div class="admin-card">
        <h2>Add New Job Listing</h2>

        <?php include __DIR__ . '/../includes/addJob_form.php'; ?>

        <a href="../index.php" class="admin-back">← Back to Home</a>
    </div>
</div>
</body>
</html>
