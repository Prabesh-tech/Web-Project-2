<section class="hero">
    <div class="hero-content">
        <div class="hero-copy">
            <span class="eyebrow">Discover Your Next Opportunity</span>
            <h1>Find the best jobs across Nepal</h1>
            <p>Browse verified openings, connect with employers, and advance your career with confidence.</p>
            <form method="GET" action="search.php" class="search-form">
                <input type="search" name="q" placeholder="Search jobs, skills, companies..." class="search-input">
                <button type="submit" class="btn-search">Search</button>
            </form>
        </div>
    </div>
</section>

<section class="jobs-page">
    <div class="section-heading">
        <span class="eyebrow">Latest Jobs</span>
        <h1>Explore current job openings</h1>
        <p>Only the latest jobs are shown here so you can find what matters quickly.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($jobs)): ?>
        <div class="search-hint">No jobs are available at the moment. Please check back soon.</div>
    <?php else: ?>
        <div class="job-grid">
            <?php foreach ($jobs as $job): ?>
                <?php
                    $closingText = '—';
                    if (!empty($job['closingDate'])) {
                        try {
                            $diff = (new DateTime($job['closingDate']))->diff(new DateTime());
                            $closingText = $diff->days . ' days left';
                        } catch (Exception $e) {
                            $closingText = '—';
                        }
                    }

                    // Raw values from DB
                    $levelRaw = trim($job['experience'] ?? '');
                    $typeRaw = trim($job['jobType'] ?? '');
                    $salaryRaw = trim($job['salary'] ?? '');
                    $salaryMaxRaw = trim($job['salaryMax'] ?? '');

                    // Determine display for experience: Senior / Mid / Entry
                    $levelLabel = '';
                    $lrLower = strtolower($levelRaw);
                    if ($lrLower === 'fresher' || strpos($lrLower, 'fresher') !== false || $lrLower === 'intern') {
                        $levelLabel = 'Entry';
                    } else {
                        // try to parse a numeric year value from strings like '3-5 years' or '2 years'
                        if (preg_match('/(\d+)/', $levelRaw, $m)) {
                            $years = intval($m[1]);
                            if ($years >= 5) {
                                $levelLabel = 'Senior';
                            } elseif ($years >= 2) {
                                $levelLabel = 'Mid';
                            } else {
                                $levelLabel = 'Entry';
                            }
                        } else {
                            // fallback: use the raw string if it contains senior/mid/entry
                            if (strpos($lrLower, 'senior') !== false) $levelLabel = 'Senior';
                            elseif (strpos($lrLower, 'mid') !== false) $levelLabel = 'Mid';
                            elseif (strpos($lrLower, 'entry') !== false) $levelLabel = 'Entry';
                            else $levelLabel = htmlspecialchars($levelRaw);
                        }
                    }

                    // Prepare job type label
                    $type = htmlspecialchars($typeRaw);

                    // Prepare salary display
                    $salaryDisplay = '';
                    if ($salaryRaw !== '') {
                        $s1 = is_numeric($salaryRaw) ? number_format(intval($salaryRaw)) : htmlspecialchars($salaryRaw);
                        if ($salaryMaxRaw !== '') {
                            $s2 = is_numeric($salaryMaxRaw) ? number_format(intval($salaryMaxRaw)) : htmlspecialchars($salaryMaxRaw);
                            $salaryDisplay = 'Rs. ' . $s1 . ' - ' . $s2;
                        } else {
                            $salaryDisplay = 'Rs. ' . $s1;
                        }
                    }

                    $level = htmlspecialchars($levelLabel);
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
                            <div class="meta-item">⏳ <?= $closingText ?></div>
                        </div>

                        <div class="badges-row">
                            <?php if (!empty($type)): ?>
                                <span class="badge badge-type"><?= $type ?></span>
                            <?php endif; ?>
                            <?php if (!empty($level)): ?>
                                <span class="badge badge-level"><?= $level ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="action-row">
                            <a href="job.php?id=<?= intval($job['id']) ?>" class="btn btn-primary btn-view-job">View Job</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
