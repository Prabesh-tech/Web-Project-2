<?php
$jobs = isset($jobs) ? $jobs : [];
$error = isset($error) ? $error : '';
$archivedJobs = array_filter($jobs, fn($job) => intval($job['isArchived'] ?? 0) === 1);
?>

<div class="archived-jobs-container">
    <div class="page-header">
        <h1>Archived Jobs</h1>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (empty($archivedJobs)): ?>
        <div class="search-hint">There are currently no archived jobs.</div>
    <?php else: ?>
        <div class="job-grid">
            <?php foreach ($archivedJobs as $job): ?>
                <?php
                    $salaryDisplay = '';
                    $salaryRaw = trim($job['salary'] ?? '');
                    $salaryMaxRaw = trim($job['salaryMax'] ?? '');
                    if ($salaryRaw !== '') {
                        $s1 = is_numeric($salaryRaw) ? number_format(intval($salaryRaw)) : htmlspecialchars($salaryRaw);
                        if ($salaryMaxRaw !== '') {
                            $s2 = is_numeric($salaryMaxRaw) ? number_format(intval($salaryMaxRaw)) : htmlspecialchars($salaryMaxRaw);
                            $salaryDisplay = 'Rs. ' . $s1 . ' - ' . $s2;
                        } else {
                            $salaryDisplay = 'Rs. ' . $s1;
                        }
                    }
                ?>
                <article class="job-card card-new">
                    <div class="card-header">
                        <div class="logo-box">
                            <img src="<?= htmlspecialchars($job['logo'] ?? 'assets/images/Image3.png') ?>" alt="<?= htmlspecialchars($job['companyName'] ?? '') ?>"/>
                        </div>
                        <div class="header-text">
                            <div class="job-title-row">
                                <h4 class="job-title"><?= htmlspecialchars($job['title']) ?></h4>
                            </div>
                            <div class="company-name"><?= htmlspecialchars($job['companyName'] ?? '') ?></div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="meta-row">
                            <div class="meta-item">📍 <?= htmlspecialchars($job['location'] ?? '—') ?></div>
                            <?php if (!empty($salaryDisplay)): ?>
                                <div class="meta-item">💰 <?= $salaryDisplay ?></div>
                            <?php endif; ?>
                            <div class="meta-item">📦 Archived</div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="action-row">
                            <a href="job.php?id=<?= intval($job['id']) ?>" class="btn btn-primary btn-view-job">View Job</a>
                            <?php if (isset($_SESSION['user']) && intval($_SESSION['user']['id'] ?? 0) === intval($job['postedBy'] ?? 0)): ?>
                                <a href="job.php?id=<?= intval($job['id']) ?>&action=unarchive" class="btn btn-warning btn-small">Restore</a>
                                <a href="job.php?id=<?= intval($job['id']) ?>&action=delete" class="btn btn-danger btn-small" onclick="return confirm('Delete this archived job permanently?');">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
