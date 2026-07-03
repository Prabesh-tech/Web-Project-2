<?php
$error = $error ?? '';
$jobs = $jobs ?? [];
$categoryName = $categoryName ?? 'Category';
?>

<section class="jobs-page">
    <div class="section-heading">
        <span class="eyebrow">Category</span>
        <h1><?= htmlspecialchars($categoryName) ?></h1>
        <p>Showing the latest jobs for this category.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($jobs)): ?>
        <div class="search-hint">No jobs are available in this category right now.</div>
    <?php else: ?>
        <div class="job-grid category-job-grid">
            <?php foreach ($jobs as $job): ?>
                <article class="job-card category-job-card">
                    <div class="job-card-top">
                        <div class="job-card-logo">
                            <?= htmlspecialchars(substr($job['companyName'] ?? 'Company', 0, 1)) ?>
                        </div>
                        <div class="job-card-title-group">
                            <h2><?= htmlspecialchars($job['title']) ?></h2>
                            <p class="job-card-company"><?= htmlspecialchars($job['companyName'] ?? 'Company name not available') ?></p>
                        </div>
                        <span class="job-card-badge">★</span>
                    </div>
                    <div class="job-card-details">
                        <div class="job-detail-row">
                            <span>📍 <?= htmlspecialchars($job['location'] ?? 'Location not specified') ?></span>
                            <span>⏳ <?= htmlspecialchars(!empty($job['closingDate']) ? $job['closingDate'] : 'No deadline') ?></span>
                        </div>
                        <div class="job-detail-row">
                            <span>💼 <?= htmlspecialchars($job['jobType'] ?? 'Full Time') ?></span>
                            <span><?= htmlspecialchars($job['salary'] ?? 'Salary not specified') ?></span>
                        </div>
                    </div>
                    <?php $description = trim($job['description'] ?? ''); ?>
                    <p><?= nl2br(htmlspecialchars(substr($description, 0, 200))) ?><?= strlen($description) > 200 ? '...' : '' ?></p>
                    <a href="job.php?id=<?= intval($job['id']) ?>" class="btn btn-secondary">View details</a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
