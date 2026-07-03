<?php
$error = $error ?? '';
$job = $job ?? null;
$categoryName = $categoryName ?? '';
$success = $success ?? '';
$currentUserRole = intval($_SESSION['user']['role'] ?? 0);
$currentUserId = intval($_SESSION['user']['id'] ?? 0);
$canManageJob = isset($_SESSION['user']) && (
    (($currentUserId === intval($job['postedBy'] ?? 0)) && $currentUserRole === 1) || in_array($currentUserRole, [2, 3], true)
);
$jobIsArchived = !empty($job) && intval($job['isArchived'] ?? 0) === 1;
?>

<section class="job-details-page">
    <?php if ($error || !$job): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error ?: 'Job not found.') ?></div>
    <?php else: ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($jobIsArchived): ?>
            <div class="alert alert-warning">This job has been archived and is currently hidden from public listings.</div>
        <?php endif; ?>

        <div class="job-details-card">
            <div class="job-details-header">
                    <div class="job-details-top">
                    <div class="job-logo-card">
                        <img src="<?= htmlspecialchars($job['companyLogo'] ?? 'assets/images/Image3.png') ?>" alt="<?= htmlspecialchars($job['companyName'] ?? 'Employer logo') ?>">
                    </div>
                    <div>
                        <span class="eyebrow">Job Details</span>
                        <h1><?= htmlspecialchars($job['title']) ?></h1>
                        <p class="job-details-meta">
                            <?= htmlspecialchars($job['companyName'] ?? 'Company unknown') ?>
                            <?php if (!empty($job['categoryName'])): ?> &bull; <?= htmlspecialchars($job['categoryName']) ?><?php endif; ?>
                        </p>
                    </div>
                </div>

                <div class="job-details-right">
                    <div class="badge-pill badge-type"><?= htmlspecialchars($job['jobType'] ?? 'Full Time') ?></div>
                    <div class="salary-panel">
                        <span class="salary-label">Salary</span>
                        <strong class="salary-value">
                            <?php
                                $salaryText = 'Nrs. Not specified';
                                if (!empty($job['salary'])) {
                                    $salaryText = 'Nrs. ' . number_format(intval($job['salary']));
                                    if (!empty($job['salaryMax'])) {
                                        $salaryText .= ' - ' . number_format(intval($job['salaryMax']));
                                    }
                                }
                            ?>
                            <?= htmlspecialchars($salaryText) ?>
                        </strong>
                    </div>
                    <div class="action-buttons">
                        <?php if (!$jobIsArchived): ?>
                            <?php if (!empty($_SESSION['user']['id'])): ?>
                                <a href="apply.php?jobId=<?= intval($job['id']) ?>" class="btn btn-primary">Apply Now</a>
                            <?php else: ?>
                                <a href="login.php?redirect=<?= urlencode('apply.php?jobId=' . intval($job['id']) . '&step=1') ?>" class="btn btn-primary">Login to Apply</a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="btn btn-secondary disabled">Applications Closed</span>
                        <?php endif; ?>
                        <?php if ($canManageJob): ?>
                            <a href="editJob.php?id=<?= intval($job['id']) ?>" class="btn btn-secondary">Edit Job</a>
                            <a href="job.php?id=<?= intval($job['id']) ?>&action=<?= $jobIsArchived ? 'unarchive' : 'archive' ?>" class="btn btn-warning">
                                <?= $jobIsArchived ? 'Unarchive' : 'Archive' ?>
                            </a>
                            <a href="job.php?id=<?= intval($job['id']) ?>&action=delete" class="btn btn-danger" onclick="return confirm('Delete this job permanently?');">Delete Job</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="job-details-info-grid">
                <div class="job-details-field">
                    <strong>Location</strong>
                    <span><?= htmlspecialchars($job['location'] ?? 'Location not specified') ?></span>
                </div>
                <div class="job-details-field">
                    <strong>Job Level</strong>
                    <span><?= htmlspecialchars($job['experience'] ?? 'Not specified') ?></span>
                </div>
                <div class="job-details-field">
                    <strong>Category</strong>
                    <span><?= htmlspecialchars($job['categoryName'] ?? 'Not specified') ?></span>
                </div>
                <div class="job-details-field">
                    <strong>Job Shift</strong>
                    <span><?= htmlspecialchars($job['jobType'] ?? 'Day') ?></span>
                </div>
                <div class="job-details-field">
                    <strong>Openings</strong>
                    <span><?= htmlspecialchars($job['vacancies'] ?? '1') ?></span>
                </div>
                <div class="job-details-field">
                    <strong>Deadline</strong>
                    <span><?= htmlspecialchars(date('M j, Y', strtotime($job['closingDate'] ?? 'now'))) ?></span>
                </div>
            </div>

            <div class="job-details-description">
                <h2>Job Overview</h2>
                <p><?= nl2br(htmlspecialchars($job['description'] ?? 'No description provided.')) ?></p>
            </div>

            <?php if (!empty($jobRequirements)): ?>
                <div class="job-details-skills">
                    <h2>Required Skills</h2>
                    <div class="skills-list">
                        <?php foreach ($jobRequirements as $skill): ?>
                            <span class="skill-pill"><?= htmlspecialchars($skill) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($canManageJob): ?>
                <div class="job-applications-panel">
                    <h2>Applicant Review</h2>
                    <?php if (empty($jobApplications)): ?>
                        <div class="alert alert-info">No applications have been submitted for this job yet.</div>
                    <?php else: ?>
                        <div class="applications-table-wrapper">
                            <table class="applications-table">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Applied On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jobApplications as $application): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($application['fullName'] ?: $application['applicantUsername'] ?: 'Unknown') ?></td>
                                            <td><?= htmlspecialchars($application['email']) ?></td>
                                            <td><?= htmlspecialchars($application['status']) ?></td>
                                            <td><?= htmlspecialchars(date('M d, Y', strtotime($application['appliedAt'] ?? 'now'))) ?></td>
                                            <td class="application-actions-cell">
                                                <?php if ($application['status'] !== 'Shortlisted'): ?>
                                                    <a href="job.php?id=<?= intval($job['id']) ?>&action=shortlist&applicationId=<?= intval($application['id']) ?>" class="btn btn-secondary" onclick="return confirm('Mark this application as shortlisted?');">Shortlist</a>
                                                <?php endif; ?>

                                                <?php if ($application['status'] !== 'Rejected'): ?>
                                                    <a href="job.php?id=<?= intval($job['id']) ?>&action=reject&applicationId=<?= intval($application['id']) ?>" class="btn btn-danger" onclick="return confirm('Reject this application?');">Reject</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<style>
.job-details-page .action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-end; /* align action buttons to the right */
}

@media (max-width: 720px) {
    .job-details-page .action-buttons {
        justify-content: center; /* center on small screens */
        gap: 8px;
    }
}

.job-details-page .btn-warning {
    background: #f59e0b;
    color: #111827;
    border: none;
}

.job-details-page .btn-warning:hover {
    background: #d97706;
    color: #ffffff;
}

.job-details-page .disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

.alert-warning {
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.35);
    color: #fcd34d;
}
</style>
