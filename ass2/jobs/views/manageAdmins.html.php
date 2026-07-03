<?php
$adminUsers = isset($adminUsers) ? $adminUsers : [];
$error = isset($error) ? $error : '';
successMessage:
$successMessage = isset($successMessage) ? $successMessage : '';
$currentRole = intval($_SESSION['user']['role'] ?? 0);
?>

<div class="manage-admins-container">
    <div class="page-header">
        <h1>Manage Admins</h1>
    </div>

    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($showAdminForm): ?>
        <div class="admin-form-section">
            <h2>Add New Admin</h2>
            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <input type="hidden" name="admin_role" value="admin">
                <div class="form-group">
                    <label>Admin Role</label>
                    <div class="radio-group">
                        <label><input type="radio" name="admin_role" value="admin" checked disabled> Admin</label>
                    </div>
                    <p class="text-note">Only one Super Admin exists; new accounts are created as regular Admins.</p>
                </div>
                <button type="submit" class="btn btn-primary">Create Admin</button>
            </form>
        </div>
    <?php else: ?>
        <div class="admin-form-section">
            <h2>Add New Admin</h2>
            <p class="text-muted">Only a Super Admin can create new admin accounts.</p>
        </div>
    <?php endif; ?>

    <div class="table-responsive admin-list-section">
        <h2>Existing Admin Accounts</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($adminUsers)): ?>
                    <?php foreach ($adminUsers as $admin): ?>
                        <tr>
                            <td><?= intval($admin['id']) ?></td>
                            <td><?= htmlspecialchars($admin['username']) ?></td>
                            <td><?= htmlspecialchars($admin['email']) ?></td>
                            <td><?= intval($admin['role']) === 2 ? 'Admin' : 'Super Admin' ?></td>
                            <td><?= date('M d, Y', strtotime($admin['createdAt'])) ?></td>
                            <td>
                                <?php if (intval($admin['id']) !== intval($_SESSION['user']['id'] ?? 0)): ?>
                                    <form method="POST" class="inline-form" onsubmit="return confirm('Delete this admin account?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= intval($admin['id']) ?>">
                                        <button type="submit" class="btn btn-danger btn-small">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Current</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No admin accounts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
