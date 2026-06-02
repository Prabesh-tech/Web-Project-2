<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

//  Restrict to admins or Super Admin (isAdmin 1 or 2)
if (!((!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
    (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin'))) {
    header("Location: ../login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$error = '';
$success = false;

// Fetch existing job listing
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$id]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Job not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = intval($_POST['categoryId']);
    $salary = trim($_POST['salary']);
    $location = trim($_POST['location']);
    $jobType = trim($_POST['jobType']);
    $closingDate = $_POST['closingDate'];

    if ($error === '') {
        try {
            $stmt = $pdo->prepare("UPDATE jobs 
                                   SET title=?, description=?, categoryId=?, salary=?, location=?, jobType=?, closingDate=? 
                                   WHERE id=?");
            $stmt->execute([$title, $description, $categoryId, $salary, $location, $jobType, $closingDate, $id]);
            $success = true;
            $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
            $stmt->execute([$id]);
            $auction = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Job</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <h1>Edit Job Listing</h1>
        <a href="../index.php" class="btn-back">← Back to index page</a>

        <?php if ($success): ?>
            <div class="auth-success">✅ Job updated successfully!</div>
        <?php elseif ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="auth-form">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required>

            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($auction['description']) ?></textarea>

            <label>Category</label>
            <select name="categoryId" required>
                <?php
                $cats = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($cats as $c) {
                    $selected = $c['id'] == $auction['categoryId'] ? 'selected' : '';
                    echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                }
                ?>
            </select>

            <label>Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($auction['location'] ?? '') ?>" placeholder="e.g., Kathmandu" required>

            <label>Job Type</label>
            <select name="jobType" required>
                <option value="Full Time" <?= $auction['jobType'] === 'Full Time' ? 'selected' : '' ?>>Full Time</option>
                <option value="Part Time" <?= $auction['jobType'] === 'Part Time' ? 'selected' : '' ?>>Part Time</option>
                <option value="Contract" <?= $auction['jobType'] === 'Contract' ? 'selected' : '' ?>>Contract</option>
                <option value="Freelance" <?= $auction['jobType'] === 'Freelance' ? 'selected' : '' ?>>Freelance</option>
            </select>

            <label>Salary</label>
            <input type="text" name="salary" value="<?= htmlspecialchars($auction['salary'] ?? '') ?>" placeholder="e.g., 50000" required>

            <label>Closing Date</label>
            <input type="date" name="closingDate" value="<?= htmlspecialchars($auction['closingDate']) ?>" required>

            <button type="submit" class="btn-login">Update Listing</button>
        </form>
    </div>
</div>
</body>
</html>
