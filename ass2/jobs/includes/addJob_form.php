<?php
// Reusable form block for creating/updating job listings.
// Expected variables: $success, $formAction, $categories, $title, $description, $categoryId, $year, $mileage, $currentBid, $endDate
?>
<?php if (!empty($success)): ?>
    <div class="auth-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="POST" action="<?= htmlspecialchars($formAction) ?>" class="job-form">
    <div class="form-group">
        <label for="title">Job Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($title ?? '') ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Job Description</label>
        <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($description ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="categoryId">Category</label>
        <select id="categoryId" name="categoryId" required>
            <option value="">Select category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= isset($categoryId) && $categoryId == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-row grid-2">
        <div class="form-group">
            <label for="year">Year</label>
            <input type="number" id="year" name="year" value="<?= htmlspecialchars($year ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="mileage">Experience / Salary Info</label>
            <input type="text" id="mileage" name="mileage" value="<?= htmlspecialchars($mileage ?? '') ?>" required>
        </div>
    </div>

    <div class="form-row grid-2">
        <div class="form-group">
            <label for="currentBid">Salary Range</label>
            <input type="text" id="currentBid" name="currentBid" value="<?= htmlspecialchars($currentBid ?? '') ?>" placeholder="e.g., 30,000 - 40,000" required>
        </div>
        <div class="form-group">
            <label for="endDate">Application Deadline</label>
            <input type="datetime-local" id="endDate" name="endDate" value="<?= htmlspecialchars($endDate ?? '') ?>" required>
        </div>
    </div>

    <button type="submit" class="btn-primary">Submit Job</button>
</form>
