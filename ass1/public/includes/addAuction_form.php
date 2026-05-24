<?php
// This file contains only the Add Auction form fragment so it can be embedded in a modal.
?>

<?php if (isset($success) && $success): ?>
    <div class="auth-success">✅ Auction added successfully!</div>
<?php elseif (isset($error) && $error): ?>
    <div class="auth-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form embed-form" action="<?= htmlspecialchars($formAction ?? 'addAuction.php') ?>">
    <input type="hidden" name="formType" value="<?= htmlspecialchars($formType ?? 'addAuction') ?>">
    <input type="text" name="title" placeholder="Car Title" value="<?= htmlspecialchars($title ?? '') ?>" required>
    <textarea name="description" placeholder="Description"><?= htmlspecialchars($description ?? '') ?></textarea>
    <select name="categoryId" required>
        <option value="">Select Existing Category</option>
        <?php foreach ($categories as $c): ?>
            <option value="<?= htmlspecialchars($c['id']) ?>" <?= isset($categoryId) && intval($categoryId) === intval($c['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (empty($categories)): ?>
        <div class="auth-error">No categories available. Please add a category before creating an auction.</div>
        <button type="submit" class="btn-admin" disabled>Add Auction</button>
    <?php else: ?>
        <input type="number" name="year" placeholder="Year" value="<?= htmlspecialchars($year ?? '') ?>" required>
        <input type="number" name="mileage" placeholder="Mileage" value="<?= htmlspecialchars($mileage ?? '') ?>" required>
        <input type="number" step="0.01" name="currentBid" placeholder="Starting Bid" value="<?= htmlspecialchars($currentBid ?? '') ?>" required>
        <input type="datetime-local" name="endDate" value="<?= htmlspecialchars($endDate ?? '') ?>" required>
        <label for="imageInput" class="file-label">Auction Image</label>
        <input id="imageInput" type="file" name="image" accept="image/*" required>
        <div class="image-preview-card">
            <div class="preview-label">Image Preview</div>
            <div class="image-preview">
                <img id="previewImage" src="assets/images/default-car.jpg" alt="Auction image preview">
            </div>
        </div>
        <div class="field-note">Maximum upload size: 20MB</div>
        <button type="submit" class="btn-admin">Add Auction</button>
    <?php endif; ?>
</form>

<script>
// Image preview script (works when the form is injected into the page)
(function(){
    const imageInput = document.getElementById('imageInput');
    const previewImage = document.getElementById('previewImage');
    if (!imageInput) return;
    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) {
            previewImage.src = 'assets/images/default-car.jpg';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
            previewImage.src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
})();
</script>
