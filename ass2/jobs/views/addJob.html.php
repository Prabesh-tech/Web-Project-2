<?php
$error = $error ?? '';
$success = $success ?? '';
$categories = $categories ?? [];
$job = $job ?? null;
?>

<div class="add-job-container">
    <form method="POST" class="add-job-form">
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <strong>Error:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <strong>Success:</strong> <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label for="title">Job Title *</label>
                <input type="text" id="title" name="title" placeholder="e.g., Senior PHP Developer" value="<?= htmlspecialchars($_POST['title'] ?? ($job['title'] ?? '')) ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="categoryId">Category *</label>
                <select id="categoryId" name="categoryId" required>
                    <option value="" disabled selected>-- Select a category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= intval($category['id']) ?>" <?= (intval($_POST['categoryId'] ?? ($job['categoryId'] ?? 0)) === intval($category['id'])) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="salary">Salary</label>
                <input type="text" id="salary" name="salary" placeholder="e.g., 50,000 - 80,000" value="<?= htmlspecialchars($_POST['salary'] ?? ($job['salary'] ?? '')) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., Kathmandu, Nepal" value="<?= htmlspecialchars($_POST['location'] ?? ($job['location'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="jobType">Job Type</label>
                <select id="jobType" name="jobType">
                    <option value="">-- Select job type --</option>
                    <option value="Full-time" <?= ($_POST['jobType'] ?? ($job['jobType'] ?? '')) === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                    <option value="Part-time" <?= ($_POST['jobType'] ?? ($job['jobType'] ?? '')) === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                    <option value="Contract" <?= ($_POST['jobType'] ?? ($job['jobType'] ?? '')) === 'Contract' ? 'selected' : '' ?>>Contract</option>
                    <option value="Freelance" <?= ($_POST['jobType'] ?? ($job['jobType'] ?? '')) === 'Freelance' ? 'selected' : '' ?>>Freelance</option>
                    <option value="Internship" <?= ($_POST['jobType'] ?? ($job['jobType'] ?? '')) === 'Internship' ? 'selected' : '' ?>>Internship</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="closingDate">Application Closing Date</label>
                <input type="date" id="closingDate" name="closingDate" value="<?= htmlspecialchars($_POST['closingDate'] ?? ($job['closingDate'] ?? '')) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group full-width">
                <label for="description">Job Description *</label>
                <textarea id="description" name="description" placeholder="Enter detailed job description..." required><?= htmlspecialchars($_POST['description'] ?? ($job['description'] ?? '')) ?></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= isset($job) ? 'Update Job' : 'Post Job' ?></button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.add-job-container {
    max-width: 800px;
    margin: 40px auto;
    padding: 30px;
}

.add-job-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.add-job-form .form-row.full-width,
.add-job-form .form-row:last-of-type {
    grid-template-columns: 1fr;
}

.add-job-form .form-group.full-width {
    grid-column: 1 / -1;
}

.add-job-form .form-group {
    display: flex;
    flex-direction: column;
}

.add-job-form label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #e2e8f0;
    font-size: 0.95rem;
}

.add-job-form input,
.add-job-form select,
.add-job-form textarea {
    padding: 12px 14px;
    border: 1px solid #334155;
    border-radius: 8px;
    background: #0f172a;
    color: #e2e8f0;
    font-family: inherit;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.add-job-form input:focus,
.add-job-form select:focus,
.add-job-form textarea:focus {
    outline: none;
    border-color: #60a5fa;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
}

.add-job-form textarea {
    resize: vertical;
    min-height: 200px;
}

.add-job-form .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
}

.add-job-form .btn {
    flex: 1;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.add-job-form .btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
    color: #ffffff;
}

.add-job-form .btn-primary:hover {
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
}

.add-job-form .btn-secondary {
    background: #1f2937;
    color: #e2e8f0;
    border: 1px solid rgba(148, 163, 184, 0.18);
}

.add-job-form .btn-secondary:hover {
    background: #111827;
}

.alert {
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.alert-error {
    background: rgba(248, 113, 113, 0.12);
    border: 1px solid rgba(248, 113, 113, 0.35);
    color: #fca5a5;
}

.alert-success {
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.35);
    color: #86efac;
}

.alert-success a {
    color: #22c55e;
    font-weight: 600;
}

@media (max-width: 768px) {
    .add-job-form .form-row {
        grid-template-columns: 1fr;
    }

    .add-job-container {
        padding: 20px;
    }
}
</style>
