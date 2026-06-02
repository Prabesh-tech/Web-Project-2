<?php
/**
 * View: Edit Category Form
 * File: editCategory.html.php
 * Form to edit an existing job category
 */
?>

<div class="form-container">
    <h1>Edit Category</h1>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>✅ Success:</strong> Category updated successfully!
        </div>
    <?php endif; ?>

    <!-- EDIT CATEGORY FORM -->
    <form method="POST" class="category-form">
        <div class="form-group">
            <label for="name">Category Name *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                placeholder="Category name" 
                required
                value="<?= htmlspecialchars($category['name'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Category description"
                rows="4"
            ><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image File</label>
            <input 
                type="text" 
                id="image" 
                name="image" 
                placeholder="e.g., assets/images/it.jpg"
                value="<?= htmlspecialchars($category['image'] ?? '') ?>"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Category</button>
            <a href="adminCategories.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
