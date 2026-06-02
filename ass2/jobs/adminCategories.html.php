<?php
/**
 * View: Admin Manage Categories
 * File: adminCategories.html.php
 * List all categories with edit/delete options
 */
?>

<div class="admin-page">
    <!-- HEADER -->
    <div class="admin-header-section">
        <div class="admin-header-content">
            <h1>Manage Job Categories</h1>
            <p class="admin-subtitle">Total Categories: <strong><?= $categoryCount ?></strong></p>
        </div>
        <a href="addCategory.php" class="btn-add">+ Add New Category</a>
    </div>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>✅ Success:</strong> Action completed successfully!
        </div>
    <?php endif; ?>

    <!-- CATEGORIES TABLE -->
    <div class="table-container">
        <?php if (!empty($categories)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Jobs Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="cell-id"><?= htmlspecialchars($cat['id']) ?></td>
                            <td class="cell-name"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="cell-desc"><?= htmlspecialchars(substr($cat['description'] ?? '', 0, 50)) ?></td>
                            <td class="cell-count"><?= htmlspecialchars($cat['job_count'] ?? 0) ?></td>
                            <td class="cell-actions">
                                <a href="editCategory.php?id=<?= $cat['id'] ?>" class="btn-edit">✏️ Edit</a>
                                <form method="POST" onsubmit="return confirm('Delete this category? This action cannot be undone.');" class="inline-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                    <button type="submit" class="btn-delete">🗑 Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No categories found. <a href="addCategory.php">Add the first category →</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>
