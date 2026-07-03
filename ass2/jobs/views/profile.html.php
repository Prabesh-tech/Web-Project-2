<?php
/**
 * User Profile View
 * Displays user profile information and applications
 *
 * @var array $user
 * @var bool $isOwnProfile
 * @var int $profileCompleteness
 * @var string $error
 * @var bool $success
 */
$user = $user ?? ['id' => 0, 'username' => '', 'email' => '', 'role' => 0, 'createdAt' => date('Y-m-d')];
$isOwnProfile = $isOwnProfile ?? false;
$profileCompleteness = $profileCompleteness ?? 0;
$error = $error ?? '';
$success = $success ?? false;
$allowedStatuses = $allowedStatuses ?? [
    'all' => 'All',
    'pending' => 'Pending',
    'shortlisted' => 'Shortlisted',
    'rejected' => 'Rejected',
    'accepted' => 'Accepted',
];
$activeStatus = $activeStatus ?? 'all';
$applications = $applications ?? [];
?>

<div class="profile-container">
    <div class="profile-header">
        <h1><?= htmlspecialchars($user['username']) ?></h1>
        <p class="member-since">Member since <?= date('M d, Y', strtotime($user['createdAt'])) ?></p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Profile updated successfully!</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="profile-grid">
        <section class="profile-section">
            <h2>Profile Information</h2>
            <dl>
                <dt>Username:</dt>
                <dd><?= htmlspecialchars($user['username']) ?></dd>
                
                <dt>Email:</dt>
                <dd><?= htmlspecialchars($user['email']) ?></dd>
                
                <dt>Account Type:</dt>
                <dd>
                    <?php 
                    $role = intval($user['role'] ?? 0);
                    echo $role === 2 ? 'Super Admin' : ($role === 1 ? 'Admin' : 'Regular User');
                    ?>
                </dd>
            </dl>

            <?php if ($isOwnProfile): ?>
                <a href="editProfile.php" class="btn btn-primary">Edit Profile</a>
            <?php endif; ?>
        </section>

        <section class="profile-section">
            <h2>Account Statistics</h2>
            <dl>
                <dt>Profile Completeness:</dt>
                <dd><?= intval($profileCompleteness) ?>%</dd>
                
                <dt>Member Status:</dt>
                <dd>Active</dd>
            </dl>
        </section>
    </div>

    <div class="profile-grid">
        <section class="profile-section full-width">
            <h2>Your Applications</h2>
            <p>Filter your application status to view pending, shortlisted, rejected, or accepted applications.</p>

            <div class="application-filters">
                <?php foreach ($allowedStatuses as $filterKey => $filterLabel): ?>
                    <a href="profile.php?id=<?= intval($user['id']) ?>&status=<?= urlencode($filterKey) ?>"
                       class="btn <?= $activeStatus === $filterKey ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= htmlspecialchars($filterLabel) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($applications)): ?>
                <div class="alert alert-info">No <?= htmlspecialchars($allowedStatuses[$activeStatus] ?? 'applications') ?> applications found.</div>
            <?php else: ?>
                <div class="applications-table-wrapper">
                    <table class="applications-table">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Status</th>
                                <th>Applied On</th>
                                <th>Last Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $application): ?>
                                <tr>
                                    <td><?= htmlspecialchars($application['jobTitle'] ?: 'Job removed') ?></td>
                                    <td><?= htmlspecialchars($application['status']) ?></td>
                                    <td><?= htmlspecialchars(date('M d, Y', strtotime($application['appliedAt'] ?? 'now'))) ?></td>
                                    <td><?= htmlspecialchars(date('M d, Y', strtotime($application['updatedAt'] ?? $application['appliedAt'] ?? 'now'))) ?></td>
                                    <td>
                                        <?php if (!empty($application['jobId'])): ?>
                                            <a href="job.php?id=<?= intval($application['jobId']) ?>" class="btn btn-secondary">View Job</a>
                                            <a href="profile.php?id=<?= intval($user['id']) ?>&viewApplicationId=<?= intval($application['id']) ?>" class="btn btn-primary">View Details</a>
                                        <?php else: ?>
                                            <span class="text-muted">No job</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <?php if (!empty($viewApplication)): ?>
        <div class="profile-grid">
            <section class="profile-section full-width">
                <h2>Application Details</h2>
                <div class="application-details-card">
                    <dl>
                        <dt>Application ID</dt>
                        <dd><?= intval($viewApplication['id']) ?></dd>

                        <dt>Job</dt>
                        <dd><?= htmlspecialchars($viewApplication['jobTitle'] ?? 'Job removed') ?></dd>

                        <dt>Status</dt>
                        <dd><?= htmlspecialchars($viewApplication['status']) ?></dd>

                        <dt>Applied On</dt>
                        <dd><?= htmlspecialchars(date('M d, Y', strtotime($viewApplication['appliedAt'] ?? 'now'))) ?></dd>

                        <dt>Last Updated</dt>
                        <dd><?= htmlspecialchars(date('M d, Y', strtotime($viewApplication['updatedAt'] ?? $viewApplication['appliedAt'] ?? 'now'))) ?></dd>

                        <dt>Full Name</dt>
                        <dd><?= htmlspecialchars($viewApplication['fullName'] ?? '') ?></dd>

                        <dt>Email</dt>
                        <dd><?= htmlspecialchars($viewApplication['email'] ?? '') ?></dd>

                        <dt>Phone</dt>
                        <dd><?= htmlspecialchars($viewApplication['phone'] ?? '') ?></dd>

                        <dt>Cover Letter</dt>
                        <dd><?= nl2br(htmlspecialchars($viewApplication['coverLetter'] ?? '')) ?></dd>
                    </dl>
                </div>
            </section>
        </div>
    <?php endif; ?>
</div>
