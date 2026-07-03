<div class="admin-categories">
    <h1>Manage Job Categories</h1>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success && isset($_GET['status'])): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> Category <?= htmlspecialchars($_GET['status']) ?> successfully.
        </div>
    <?php endif; ?>

    <div class="categories-container">
        <div class="add-category-section">
            <h2><?= isset($editCategory) ? 'Edit Category' : 'Add New Category' ?></h2>
            <form method="POST" class="category-form">
                <input type="hidden" name="action" value="<?= isset($editCategory) ? 'edit' : 'add' ?>">
                <?php if (isset($editCategory)): ?>
                    <input type="hidden" name="id" value="<?= htmlspecialchars($editCategory['id']) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="categoryName">Category Name *</label>
                    <input 
                        type="text" 
                        id="categoryName" 
                        name="name" 
                        placeholder="Enter category name" 
                        value="<?= isset($editCategory) ? htmlspecialchars($editCategory['name']) : '' ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="categoryDesc">Description</label>
                    <textarea 
                        id="categoryDesc" 
                        name="description" 
                        placeholder="Enter category description" 
                        rows="4"
                    ><?= isset($editCategory) ? htmlspecialchars($editCategory['description']) : '' ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <?= isset($editCategory) ? 'Update Category' : 'Add Category' ?>
                    </button>
                    <?php if (isset($editCategory)): ?>
                        <a href="adminCategories.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="categories-list-section">
            <h2>All Categories (<?= count($categories) ?>)</h2>

            <?php if (!empty($categories)): ?>
                <div class="categories-table-wrapper">
                    <table class="categories-table">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($category['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(substr($category['description'] ?? '', 0, 50)) ?>
                                        <?php if (strlen($category['description'] ?? '') > 50): ?>...<?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="adminCategories.php?action=edit&id=<?= htmlspecialchars($category['id']) ?>" class="btn btn-sm btn-edit">Edit</a>
                                        <form method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($category['id']) ?>">
                                            <button type="submit" class="btn btn-sm btn-delete">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="no-data">No categories found. Create one to get started!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.admin-categories {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.admin-categories h1 {
    color: #f1f5f9;
    margin-bottom: 30px;
    font-size: 2.5rem;
}

.admin-categories h2 {
    color: #f1f5f9;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: #fca5a5;
    border-left-color: #ef4444;
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: #86efac;
    border-left-color: #10b981;
}

.categories-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.add-category-section,
.categories-list-section {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid #374151;
    border-radius: 12px;
    padding: 25px;
}

.category-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    color: #f1f5f9;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-group input,
.form-group textarea {
    padding: 12px;
    background: #0f172a;
    color: #f1f5f9;
    border: 1px solid #374151;
    border-radius: 6px;
    font-family: inherit;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
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
    flex: 1;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
}

.btn-secondary {
    background: #374151;
    color: #f1f5f9;
    flex: 1;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-sm {
    padding: 8px 12px;
    font-size: 0.85rem;
}

.btn-edit {
    background: #3b82f6;
    color: #ffffff;
}

.btn-edit:hover {
    background: #2563eb;
}

.btn-delete {
    background: #ef4444;
    color: #ffffff;
}

.btn-delete:hover {
    background: #dc2626;
}

.categories-table-wrapper {
    overflow-x: auto;
}

.categories-table {
    width: 100%;
    border-collapse: collapse;
}

.categories-table thead {
    background: rgba(31, 41, 55, 0.5);
}

.categories-table th {
    color: #9ca3af;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    border-bottom: 2px solid #374151;
    text-transform: uppercase;
    font-size: 0.85rem;
}

.categories-table td {
    color: #f1f5f9;
    padding: 12px;
    border-bottom: 1px solid #374151;
}

.categories-table tbody tr:hover {
    background: rgba(16, 185, 129, 0.1);
}

.action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
}

.inline-form {
    display: inline;
}

.no-data {
    color: #9ca3af;
    text-align: center;
    padding: 30px;
    font-style: italic;
}

@media (max-width: 1024px) {
    .categories-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .admin-categories {
        padding: 15px;
    }

    .admin-categories h1 {
        font-size: 1.8rem;
    }

    .add-category-section,
    .categories-list-section {
        padding: 15px;
    }

    .form-actions {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .action-buttons {
        flex-direction: column;
    }

    .categories-table {
        font-size: 0.9rem;
    }

    .categories-table th,
    .categories-table td {
        padding: 8px;
    }
}
</style>
