<?php
/**
 * Manage Users View (Admin)
 * Lists all users with edit/delete options
 */
?>

<div class="manage-users-container">
    <div class="page-header">
        <h1>Manage Users</h1>
        <a href="addUser.php" class="btn btn-primary">Add New User</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">Operation completed successfully!</div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="search-section">
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by username or email" 
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="search-input">
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
                                <span class="role-badge role-<?= intval($u['isAdmin']) === 2 ? 'super' : (intval($u['isAdmin']) === 1 ? 'admin' : 'user') ?>">
                                    <?= intval($u['isAdmin']) === 2 ? 'Super Admin' : (intval($u['isAdmin']) === 1 ? 'Admin' : 'User') ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($u['createdAt'])) ?></td>
                            <td class="actions">
                                <a href="editUser.php?id=<?= intval($u['id']) ?>" class="btn-link">Edit</a>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
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

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= intval($i) ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                    <?= intval($i) ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.manage-users-container {
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    border-bottom: 2px solid #e0e0e0;
    padding-bottom: 20px;
}

.page-header h1 {
    margin: 0;
}

.search-section {
    margin-bottom: 20px;
}

.search-form {
    display: flex;
    gap: 10px;
}

.search-input {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
}

.table-responsive {
    overflow-x: auto;
    margin-bottom: 20px;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.users-table thead {
    background-color: #f8f9fa;
}

.users-table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
    border-bottom: 2px solid #dee2e6;
}

.users-table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}

.users-table tbody tr:hover {
    background-color: #f5f5f5;
}

.role-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.role-user {
    background: #e3f2fd;
    color: #1976d2;
}

.role-admin {
    background: #fff3e0;
    color: #f57c00;
}

.role-super {
    background: #f3e5f5;
    color: #7b1fa2;
}

.actions {
    display: flex;
    gap: 10px;
}

.btn-link {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    padding: 0;
    font-size: 14px;
    text-decoration: underline;
}

.btn-link:hover {
    color: #0056b3;
}

.btn-danger {
    color: #dc3545;
}

.btn-danger:hover {
    color: #c82333;
}

.text-center {
    text-align: center;
    color: #666;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    text-decoration: none;
    color: #007bff;
    background: white;
}

.page-link:hover {
    background-color: #f5f5f5;
}

.page-link.active {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.alert {
    padding: 12px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #545b62;
}
</style>
