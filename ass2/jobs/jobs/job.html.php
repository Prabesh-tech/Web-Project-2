<?php
/**
 * View: Job Detail Page
 * File: job.html.php
 * Shows single job with details and apply button
 */
?>

<div class="job-detail">
    <div class="job-header">
        <div class="job-title-section">
            <h1><?= htmlspecialchars($job['title']) ?></h1>
            <p class="company-name"><?= htmlspecialchars($job['companyName']) ?></p>
            <p class="job-meta">
                <span class="location">📍 <?= htmlspecialchars($job['location'] ?? 'Not specified') ?></span>
                <span class="posted-date">📅 Posted: <?= date('M d, Y', strtotime($job['createdAt'])) ?></span>
            </p>
        </div>
        <div class="job-salary-section">
            <?php if (!empty($job['salary'])): ?>
                <div class="salary-badge"><?= htmlspecialchars($job['salary']) ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="job-content-wrapper">
        <!-- MAIN CONTENT -->
        <div class="job-content">
            <h2>Job Description</h2>
            <div class="job-description">
                <?= nl2br(htmlspecialchars($job['description'])) ?>
            </div>

            <h2>Requirements</h2>
            <ul class="job-requirements">
                <li>Professional communication skills</li>
                <li>Team collaboration ability</li>
                <li>Problem-solving mindset</li>
            </ul>

            <h2>Benefits</h2>
            <ul class="job-benefits">
                <li>Competitive salary</li>
                <li>Health insurance</li>
                <li>Professional development</li>
                <li>Flexible working hours</li>
            </ul>
        </div>

        <!-- SIDEBAR -->
        <aside class="job-sidebar">
            <div class="sidebar-card apply-card">
                <h3>Ready to apply?</h3>
                <p>Click below to submit your application for this position.</p>
                <a href="jobs/apply.php?id=<?= $job['id'] ?>" class="btn-apply">Apply Now</a>
            </div>

            <div class="sidebar-card job-info-card">
                <h3>Job Information</h3>
                <div class="info-item">
                    <strong>Job Type:</strong>
                    <span>Full-time</span>
                </div>
                <div class="info-item">
                    <strong>Category:</strong>
                    <span><?= htmlspecialchars($categoryName) ?></span>
                </div>
                <div class="info-item">
                    <strong>Location:</strong>
                    <span><?= htmlspecialchars($job['location'] ?? 'Remote') ?></span>
                </div>
                <div class="info-item">
                    <strong>Experience:</strong>
                    <span>2-5 years</span>
                </div>
            </div>

            <div class="sidebar-card share-card">
                <h3>Share This Job</h3>
                <div class="share-buttons">
                    <a href="#" class="share-btn facebook">Facebook</a>
                    <a href="#" class="share-btn twitter">Twitter</a>
                    <a href="#" class="share-btn linkedin">LinkedIn</a>
                </div>
            </div>
        </aside>
    </div>

    <!-- RELATED JOBS -->
    <?php if (!empty($relatedJobs)): ?>
        <section class="related-jobs">
            <h2>Similar Jobs</h2>
            <div class="jobs-grid">
                <?php foreach ($relatedJobs as $relJob): ?>
                    <div class="job-card-mini">
                        <h4><?= htmlspecialchars($relJob['title']) ?></h4>
                        <p class="company"><?= htmlspecialchars($relJob['companyName']) ?></p>
                        <p class="salary"><?= htmlspecialchars($relJob['salary']) ?></p>
                        <a href="jobs/job.php?id=<?= $relJob['id'] ?>" class="btn-view">View Job</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
