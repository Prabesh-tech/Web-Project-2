<?php
/**
 * View: Add Job Form
 * File: addJob.html.php
 * Form to add a new job
 */
?>

<div class="form-container">
    <h1><?= htmlspecialchars($pageTitle ?? 'Add New Job') ?></h1>
    
    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>✅ Success:</strong> Job posted successfully!
        </div>
    <?php endif; ?>

    <!-- ADD JOB FORM -->
    <form method="POST" class="job-form">
        <div class="form-group">
            <label for="title">Job Title *</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                placeholder="e.g., Senior Developer" 
                required
                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
            >
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="categoryId">Category *</label>
                <select id="categoryId" name="categoryId" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_POST['categoryId'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="salary">Salary</label>
                <input 
                    type="text" 
                    id="salary" 
                    name="salary" 
                    placeholder="e.g., $50,000 - $80,000"
                    value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="companyName">Company Name</label>
                <input 
                    type="text" 
                    id="companyName" 
                    name="companyName" 
                    placeholder="Company name"
                    value="<?= htmlspecialchars($_POST['companyName'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    placeholder="e.g., Kathmandu, Nepal"
                    value="<?= htmlspecialchars($_POST['location'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="description">Job Description *</label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Detailed job description, requirements, and responsibilities"
                rows="8"
                required
            ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Post Job</button>
            <a href="jobs/manageJobs.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
