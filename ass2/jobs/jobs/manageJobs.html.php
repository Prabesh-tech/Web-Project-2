<?php
/**
 * View: Manage Jobs (Admin)
 * File: manageJobs.html.php
 * List all jobs with edit/delete options
 */
?>

<div class="admin-page">
    <!-- HEADER -->
    <div class="admin-header-section">
        <div class="admin-header-content">
            <h1>Manage Jobs</h1>
            <p class="admin-subtitle">Total Jobs: <strong><?= $jobCount ?></strong></p>
        </div>
        <a href="addJob.php" class="btn-add">+ Post New Job</a>
    </div>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>✅ Success:</strong> Action completed successfully!
        </div>
    <?php endif; ?>

    <!-- JOBS TABLE -->
    <div class="table-container">
        <?php if (!empty($jobs)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Category</th>
                        <th>Salary</th>
                        <th>Posted Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jobs as $j): ?>
                        <tr>
                            <td class="cell-id"><?= htmlspecialchars($j['id']) ?></td>
                            <td class="cell-title"><?= htmlspecialchars($j['title']) ?></td>
                            <td class="cell-company"><?= htmlspecialchars($j['companyName'] ?? 'N/A') ?></td>
                            <td class="cell-category"><?= htmlspecialchars($j['categoryName'] ?? 'Uncategorized') ?></td>
                            <td class="cell-salary"><?= htmlspecialchars($j['salary'] ?? '-') ?></td>
                            <td class="cell-date"><?= date('M d, Y', strtotime($j['createdAt'])) ?></td>
                            <td class="cell-actions">
                                <a href="editJob.php?id=<?= $j['id'] ?>" class="btn-edit">✏️ Edit</a>
                                <a href="jobs/job.php?id=<?= $j['id'] ?>" class="btn-view" target="_blank">👁️ View</a>
                                <form method="POST" onsubmit="return confirm('Delete this job?');" class="inline-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $j['id'] ?>">
                                    <button type="submit" class="btn-delete">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No jobs posted yet. <a href="addJob.php">Post the first job →</a></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- PAGINATION -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="pagination-link <?= $currentPage === $i ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
