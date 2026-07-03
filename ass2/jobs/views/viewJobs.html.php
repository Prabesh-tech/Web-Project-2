<?php
$categories = $categories ?? [];
$error = $error ?? '';
$jobs = $jobs ?? [];
$searchQuery = $searchQuery ?? '';
$categoryId = $categoryId ?? 0;
$createdAfter = $createdAfter ?? '';
$createdBefore = $createdBefore ?? '';
$isAdmin = isset($_SESSION['user']) && in_array(intval($_SESSION['user']['role'] ?? 0), [2, 3], true);
?>

<section class="jobs-page">
    <div class="section-heading">
        <span class="eyebrow"><?= $isAdmin ? 'All Jobs' : 'Your Jobs' ?></span>
        <h1><?= $isAdmin ? 'All jobs in the system' : 'Jobs you have posted' ?></h1>
        <p><?php if ($isAdmin): ?>Review every job with edit, archive, restore, and delete controls for job owners.<?php else: ?>Manage the jobs you have posted. Only admins can view all jobs.<?php endif; ?></p>
    </div>

    <form method="GET" class="jobs-filter-form">
        <div class="filter-row">
            <div class="filter-group">
                <label for="q">Search</label>
                <input type="search" id="q" name="q" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Search jobs, companies, or categories...">
            </div>
            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= intval($category['id']) ?>" <?= intval($categoryId) === intval($category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="createdAfter">Posted After</label>
                <input type="date" id="createdAfter" name="createdAfter" value="<?= htmlspecialchars($createdAfter) ?>">
            </div>
            <div class="filter-group">
                <label for="createdBefore">Posted Before</label>
                <input type="date" id="createdBefore" name="createdBefore" value="<?= htmlspecialchars($createdBefore) ?>">
            </div>
            <div class="filter-group filter-submit">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </div>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($jobs)): ?>
        <div class="search-hint">No jobs are available at the moment. Please check back soon.</div>
    <?php else: ?>
        <?php if ($isAdmin): ?>
            <div class="job-table-wrapper">
                <table class="jobs-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Posted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <?php $currentUserRole = intval($_SESSION['user']['role'] ?? 0);
                            $canManageJob = isset($_SESSION['user']) && (
                                (intval($_SESSION['user']['id'] ?? 0) === intval($job['postedBy'] ?? 0) && $currentUserRole === 1) || in_array($currentUserRole, [2,3], true)
                            ); ?>
                            <tr>
                                <td><a href="job.php?id=<?= intval($job['id']) ?>"><?= htmlspecialchars($job['title']) ?></a></td>
                                <td><?= htmlspecialchars($job['companyName'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($job['categoryName'] ?? 'Uncategorized') ?></td>
                                <td><?= htmlspecialchars($job['location'] ?? 'Not specified') ?></td>
                                <td><?= htmlspecialchars($job['createdAt'] ?? 'Unknown') ?></td>
                                <td><?= intval($job['isArchived'] ?? 0) === 1 ? 'Archived' : 'Active' ?></td>
                                <td class="job-actions">
                                    <?php if ($canManageJob): ?>
                                        <a href="editJob.php?id=<?= intval($job['id']) ?>" class="btn btn-secondary btn-small">Edit</a>
                                        <a href="job.php?id=<?= intval($job['id']) ?>&action=<?= intval($job['isArchived'] ?? 0) === 1 ? 'unarchive' : 'archive' ?>" class="btn btn-warning btn-small">
                                            <?= intval($job['isArchived'] ?? 0) === 1 ? 'Unarchive' : 'Archive' ?>
                                        </a>
                                        <a href="job.php?id=<?= intval($job['id']) ?>&action=delete" class="btn btn-danger btn-small" onclick="return confirm('Delete this job permanently?');">Delete</a>
                                    <?php else: ?>
                                        <a href="job.php?id=<?= intval($job['id']) ?>" class="btn btn-primary btn-small">View</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="job-list">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-list-item">
                        <div class="job-list-title">
                            <a href="job.php?id=<?= intval($job['id']) ?>"><?= htmlspecialchars($job['title']) ?></a>
                            <?php if (!empty($job['companyName'])): ?>
                                <span class="job-list-company">&mdash; <?= htmlspecialchars($job['companyName']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="job-list-meta">
                            <span><?= htmlspecialchars($job['categoryName'] ?? 'Uncategorized') ?></span>
                            <span><?= htmlspecialchars($job['location'] ?? 'Location not specified') ?></span>
                            <span><?= htmlspecialchars($job['createdAt'] ?? '') ?></span>
                        </div>
                        <div class="job-list-actions">
                            <a href="job.php?id=<?= intval($job['id']) ?>" class="btn btn-primary btn-small">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
