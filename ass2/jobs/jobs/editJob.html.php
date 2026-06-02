<?php
/**
 * View: Edit Job Form
 * File: editJob.html.php
 * Form to edit an existing job
 */
?>

<div class="form-container">
    <h1>Edit Job</h1>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- SUCCESS MESSAGE -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <strong>✅ Success:</strong> Job updated successfully!
        </div>
    <?php endif; ?>

    <!-- EDIT JOB FORM -->
    <form method="POST" class="job-form">
        <div class="form-group">
            <label for="title">Job Title *</label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                placeholder="Job title" 
                required
                value="<?= htmlspecialchars($job['title'] ?? '') ?>"
            >
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="categoryId">Category *</label>
                <select id="categoryId" name="categoryId" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($job['categoryId'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
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
                    value="<?= htmlspecialchars($job['salary'] ?? '') ?>"
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
                    value="<?= htmlspecialchars($job['companyName'] ?? '') ?>"
                >
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input 
                    type="text" 
                    id="location" 
                    name="location" 
                    placeholder="e.g., Kathmandu, Nepal"
                    value="<?= htmlspecialchars($job['location'] ?? '') ?>"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="description">Job Description *</label>
            <textarea 
                id="description" 
                name="description" 
                placeholder="Detailed job description"
                rows="8"
                required
            ><?= htmlspecialchars($job['description'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Job</button>
            <a href="jobs/manageJobs.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
