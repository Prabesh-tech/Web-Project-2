<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

$jobId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$jobId) {
    die("Invalid job ID.");
}

$stmt = $pdo->prepare("SELECT a.*, c.name AS category, u.username AS postedByUser
                       FROM jobs a
                       LEFT JOIN categories c ON a.categoryId = c.id
                       LEFT JOIN users u ON a.postedBy = u.id
                       WHERE a.id = ?");
$stmt->execute([$jobId]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found");
}

$applicationError = '';
$applicationSuccess = '';
$reviewError = '';
$reviewSuccess = '';

/* Handle Application Submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'apply') {
    if (empty($_SESSION['user'])) {
        header('Location: ../login.php');
        exit;
    }

    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $coverLetter = trim($_POST['coverLetter'] ?? '');

    if ($fullName === '' || $email === '' || $phone === '') {
        $applicationError = 'Please fill in all required fields.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO applications (jobId, userId, fullName, email, phone, coverLetter, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$jobId, $_SESSION['user']['id'], $fullName, $email, $phone, $coverLetter, 'Pending']);
            $applicationSuccess = 'Your application has been submitted successfully.';
        } catch (PDOException $e) {
            $applicationError = 'Error submitting application: ' . $e->getMessage();
        }
    }
}

/* Fetch Applications */
$applications = $pdo->prepare("SELECT COUNT(*) as applicantCount FROM applications WHERE jobId = ? AND status = 'Pending'");
$applications->execute([$jobId]);
$appCount = $applications->fetch(PDO::FETCH_ASSOC);

$userRole = $_SESSION['user']['role'] ?? null;
if (empty($userRole) && isset($_SESSION['user']['isAdmin'])) {
    switch (intval($_SESSION['user']['isAdmin'])) {
        case 2:
            $userRole = 'Super Admin';
            break;
        case 1:
            $userRole = 'Admin';
            break;
        default:
            $userRole = 'User';
    }
}
?>

<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($job['title']) ?></title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<a href="../index.php" class="btn-back">← Back to Home</a>
<h1><?= htmlspecialchars($job['title']) ?></h1>
<p><?= htmlspecialchars($job['description']) ?></p>
<p>Posted by: <?= htmlspecialchars($job['postedByUser'] ?? 'Unknown') ?></p>
<p>Location: <?= htmlspecialchars($job['location'] ?? 'Not specified') ?></p>
<p>Job Type: <?= htmlspecialchars($job['jobType'] ?? 'Not specified') ?></p>
<p>Salary: <?= htmlspecialchars($job['salary'] ?? 'Not specified') ?></p>
<p>Application deadline: <?= htmlspecialchars($job['closingDate']) ?></p>

<?php if (!empty($applicationError)): ?>
    <div class="auth-error"><?= htmlspecialchars($applicationError) ?></div>
<?php endif; ?>
<?php if (!empty($applicationSuccess)): ?>
    <div class="auth-success"><?= htmlspecialchars($applicationSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($reviewError)): ?>
    <div class="auth-error"><?= htmlspecialchars($reviewError) ?></div>
<?php endif; ?>
<?php if (!empty($reviewSuccess)): ?>
    <div class="auth-success"><?= htmlspecialchars($reviewSuccess) ?></div>
<?php endif; ?>

<?php if (!empty($_SESSION['user'])): ?>
    <h3>Submit an Application</h3>
    <form method="POST">
        <input type="hidden" name="action" value="apply">
        <input type="text" name="fullName" required placeholder="Full Name">
        <input type="email" name="email" required placeholder="Email">
        <input type="tel" name="phone" required placeholder="Phone Number">
        <textarea name="coverLetter" placeholder="Cover Letter (optional)"></textarea>
        <button type="submit">Apply</button>
    </form>
<?php else: ?>
    <p><a href="../login.php">Login to apply for this job</a></p>
<?php endif; ?>

<h3>Application Statistics</h3>
<p>Total applications: <?= htmlspecialchars($appCount['applicantCount'] ?? 0) ?></p>
</body>
</html>
