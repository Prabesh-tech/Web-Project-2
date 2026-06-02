<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';

// Admin check: allow admin (1) or super admin (2) or role 'Super Admin'
$isAdmin = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
           (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
if (!$isAdmin) {
    header('Location: ../login.php');
    exit;
}

$msg = $_GET['msg'] ?? '';

$canDelete = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
             (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');

$stmt = $pdo->query("SELECT a.*, c.name AS category, u.username AS owner FROM jobs a LEFT JOIN categories c ON a.categoryId = c.id LEFT JOIN users u ON a.postedBy = u.id ORDER BY a.closingDate ASC");
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Manage Jobs</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="admin-page">
    <div class="admin-card">
        <h2>Manage Job Listings</h2>
        <?php if ($msg === 'deleted'): ?>
            <div class="auth-success">✅ Job deleted successfully.</div>
        <?php elseif ($msg === 'error'): ?>
            <div class="auth-error">An error occurred.</div>
        <?php endif; ?>

        <div class="admin-card-header">
            <div class="admin-card-actions">
                <a href="addJob.php" class="btn-admin">+ Add Job</a>
                <a href="../admin.php" class="admin-back">← Back to Dashboard</a>
            </div>
        </div>

        <?php if (empty($auctions)): ?>

            <div class="no-results">No jobs found.</div>
        <?php else: ?>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                                <th>Category</th>
                        <th>Year</th>
                        <th>Experience</th>
                        <th>Salary</th>
                        <th>Deadline</th>
                        <th>Employer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($auctions as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id']) ?></td>
                        <td><?= htmlspecialchars($a['title']) ?></td>
                        <td><?= htmlspecialchars($a['category']) ?></td>
                        <td><?= htmlspecialchars($a['year']) ?></td>
                        <td><?= htmlspecialchars($a['mileage']) ?></td>
                        <td><?= htmlspecialchars($a['salary'] ?? 'Not set') ?></td>
                        <td><?= htmlspecialchars($a['closingDate']) ?></td>
                        <td><?= htmlspecialchars($a['owner'] ?? '—') ?></td>
                        <td>
                            <div class="admin-table-actions">
                                <a href="editJob.php?id=<?= $a['id'] ?>" class="btn-admin btn-small">Edit</a>

                                <?php if ($canDelete): ?>
                                    <form method="POST" action="deleteJob.php" style="display:inline-block;margin:0;" onsubmit="return confirm('Delete this job? This cannot be undone.');">
                                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                                        <button type="submit" class="btn-admin btn-delete">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
