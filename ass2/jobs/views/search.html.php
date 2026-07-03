<?php
$query = $query ?? '';
$error = $error ?? '';
$jobs = $jobs ?? [];
?>

<section class="search-page">
    <div class="search-banner">
        <h1>Search Jobs</h1>
        <p>Search by title or description to find the right opportunity.</p>
    </div>

    <div class="search-panel">
        <form method="GET" action="search.php" class="search-form">
            <input type="search" name="q" placeholder="Search jobs, skills, companies..." value="<?= htmlspecialchars($query) ?>" class="search-input">
            <button type="submit" class="btn-search btn-primary">Search</button>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($query !== ''): ?>
        <div class="search-results">
            <?php if (!empty($jobs)): ?>
                <div class="results-count"><?= count($jobs) ?> job(s) found for "<?= htmlspecialchars($query) ?>"</div>
                <?php foreach ($jobs as $job): ?>
                    <article class="job-card">
                        <h2><?= htmlspecialchars($job['title']) ?></h2>
                        <div class="job-meta">
                            <span><?= htmlspecialchars($job['companyName'] ?? 'Company unknown') ?></span>
                            <span><?= htmlspecialchars($job['location'] ?? 'Location not specified') ?></span>
                            <span><?= htmlspecialchars($job['salary'] ?? 'Salary not specified') ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars(substr($job['description'], 0, 220))) ?><?= strlen($job['description']) > 220 ? '...' : '' ?></p>
                        <a href="job.php?id=<?= intval($job['id']) ?>" class="btn btn-secondary">View details</a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="search-hint">
                    No jobs matched your search. Try broader keywords or remove filters.
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>
