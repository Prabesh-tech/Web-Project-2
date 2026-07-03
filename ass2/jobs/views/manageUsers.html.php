<?php
/**
 * Manage Users View (Admin)
 * Lists all users with edit/delete options
 *
 * @var array $users
 * @var string $search
 * @var int $currentPage
 * @var int $totalPages
 * @var string $error
 * @var bool $success
 */
$users = isset($users) ? $users : [];
$search = isset($search) ? $search : '';
$currentPage = isset($currentPage) ? $currentPage : 1;
$totalPages = isset($totalPages) ? $totalPages : 0;
$error = isset($error) ? $error : '';
$success = isset($success) ? $success : false;
$successMessage = isset($successMessage) ? $successMessage : '';
$formValues = isset($formValues) ? $formValues : ['username' => '', 'email' => '', 'role' => 'jobseeker'];
?>

<div class="manage-users-container">
    <div class="page-header">
        <h1>Manage Users</h1>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage ?: 'Operation completed successfully!') ?></div>
    <?php endif; ?>

    <div class="manage-users-top">
        <div class="add-user-card" id="add-user-section">
            <h2>Add New User</h2>
            <form method="POST" class="add-user-form">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($formValues['username'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($formValues['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="jobseeker" <?= ($formValues['role'] ?? '') === 'jobseeker' ? 'selected' : '' ?>>Jobseeker</option>
                        <option value="employer" <?= ($formValues['role'] ?? '') === 'employer' ? 'selected' : '' ?>>Employer</option>
                        <option value="admin" <?= ($formValues['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create User</button>
            </form>
        </div>

        <div class="users-list-card">
            <div class="search-section">
                <form method="GET" class="manage-search-form">
                    <input type="text" name="search" placeholder="Search by username or email" 
                        value="<?= htmlspecialchars($search ?? '') ?>" class="manage-search-input">
                    <button type="submit" class="btn btn-secondary">Search</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Member Since</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= intval($u['id']) ?></td>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <?php
                                            $roleClass = 'user';
                                            $roleLabel = 'Jobseeker';
                                            if (intval($u['role']) === 1) {
                                                $roleClass = 'employer';
                                                $roleLabel = 'Employer';
                                            } elseif (intval($u['role']) === 2) {
                                                $roleClass = 'admin';
                                                $roleLabel = 'Admin';
                                            } elseif (intval($u['role']) === 3) {
                                                $roleClass = 'super';
                                                $roleLabel = 'Super Admin';
                                            }
                                        ?>
                                        <span class="role-badge role-<?= htmlspecialchars($roleClass) ?>">
                                            <?= htmlspecialchars($roleLabel) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($u['createdAt'])) ?></td>
                                    <td class="actions">
                                        <a href="editUser.php?id=<?= intval($u['id']) ?>" class="btn-link">Edit</a>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('Delete this user?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= intval($u['id']) ?>">
                                            <button type="submit" class="btn-link btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= intval($i) ?><?= $search !== '' ? '&search=' . urlencode($search) : '' ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                    <?= intval($i) ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
