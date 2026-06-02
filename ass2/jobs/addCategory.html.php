<?php
/**
 * View: Add Category Form
 * File: addCategory.html.php
 * Form to add a new job category
 */
?>

<div class="form-container">
    <h1><?= htmlspecialchars($pageTitle ?? 'Add New Category') ?></h1>
    
    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>✅ Success:</strong> Category added successfully!
        </div>
    <?php endif; ?>

    <!-- ADD CATEGORY FORM -->
    <form method="POST" class="category-form">
        <div class="form-group">
            <label for="name">Category Name *</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                placeholder="e.g., IT - Programming & Development" 
                required
                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Category description (optional)"
                rows="4"
            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image File</label>
            <input 
                type="text" 
                id="image" 
                name="image" 
                placeholder="e.g., assets/images/it.jpg (optional)"
                value="<?= htmlspecialchars($_POST['image'] ?? '') ?>"
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Category</button>
            <a href="adminCategories.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
