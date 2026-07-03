<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <h3>Total Users</h3>
                <p class="stat-number"><?= htmlspecialchars($stats['totalUsers'] ?? 0) ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">💼</div>
            <div class="stat-content">
                <h3>Total Jobs</h3>
                <p class="stat-number"><?= htmlspecialchars($stats['totalJobs'] ?? 0) ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📂</div>
            <div class="stat-content">
                <h3>Total Categories</h3>
                <p class="stat-number"><?= htmlspecialchars($stats['totalCategories'] ?? 0) ?></p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <h3>Total Applications</h3>
                <p class="stat-number"><?= htmlspecialchars($stats['totalApplications'] ?? 0) ?></p>
            </div>
        </div>
    </div>

    <div class="admin-sections">
        <div class="admin-section">
            <h2>Quick Actions</h2>
            <div class="action-buttons">
                <a href="adminCategories.php" class="btn btn-primary">Manage Categories</a>
                <a href="manageUsers.php" class="btn btn-primary">Manage Users</a>
                <a href="adminEnquiries.php" class="btn btn-primary">View Enquiries</a>
                <a href="addJob.php" class="btn btn-primary">Add Job</a>
            </div>
        </div>

        <div class="admin-section">
            <h2>Recent Jobs</h2>
            <?php if (!empty($recentJobs)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Company</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentJobs as $job): ?>
                            <tr>
                                <td><?= htmlspecialchars($job['title'] ?? '') ?></td>
                                <td><?= htmlspecialchars($job['companyName'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($job['createdAt'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No recent jobs found.</p>
            <?php endif; ?>
        </div>

        <div class="admin-section">
            <h2>Recent Users</h2>
            <?php if (!empty($recentUsers)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $roleMap = [0 => 'Jobseeker', 1 => 'Employer', 2 => 'Admin', 3 => 'SuperAdmin'];
                                    echo htmlspecialchars($roleMap[$user['role'] ?? 0] ?? 'Unknown');
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No recent users found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.admin-dashboard h1 {
    color: #f1f5f9;
    margin-bottom: 30px;
    font-size: 2.5rem;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid #374151;
    border-radius: 12px;
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: #10b981;
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(16, 185, 129, 0.2);
}

.stat-icon {
    font-size: 2.5rem;
    line-height: 1;
}

.stat-content h3 {
    color: #9ca3af;
    font-size: 0.9rem;
    margin: 0 0 10px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-number {
    color: #f1f5f9;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
}

.admin-sections {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.admin-section {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid #374151;
    border-radius: 12px;
    padding: 25px;
}

.admin-section h2 {
    color: #f1f5f9;
    margin-top: 0;
    margin-bottom: 20px;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.action-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    border: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.admin-table thead {
    background: rgba(31, 41, 55, 0.5);
}

.admin-table th {
    color: #9ca3af;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #374151;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.admin-table td {
    color: #f1f5f9;
    padding: 12px;
    border-bottom: 1px solid #374151;
}

.admin-table tbody tr:hover {
    background: rgba(16, 185, 129, 0.1);
}

@media (max-width: 768px) {
    .admin-dashboard h1 {
        font-size: 1.8rem;
    }

    .dashboard-stats {
        grid-template-columns: 1fr;
    }

    .stat-card {
        padding: 15px;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .admin-section {
        padding: 15px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }
}
</style>
