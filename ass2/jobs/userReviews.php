<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

// Review functionality is not available in the current database schema
require 'includes/header.php';
?>

<div class="container">
    <h1>Reviews</h1>
    <p>Review functionality is not available in the current version. Please check back later.</p>
    <a href="index.php" class="btn-back">← Back to Home</a>
</div>

<div class="page-section">
    <h1>User Reviews</h1>
    <a href="jobs/job.php?id=<?= $jobId ?>" class="btn-back">Back</a>

    <?php if (isset($_GET['success'])): ?>
        <p class="success-message">Review added successfully!</p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Review Form -->
    <form method="POST" class="form-grid">
        <div class="form-row">
            <label>Write your review</label>
            <textarea name="reviewText" required></textarea>
        </div>

        <div class="form-row">
            <label>Rating</label>
            <select name="rating" required>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐⭐</option>
                <option value="3">3 ⭐⭐⭐</option>
                <option value="4">4 ⭐⭐⭐⭐</option>
                <option value="5">5 ⭐⭐⭐⭐⭐</option>
            </select>
        </div>

        <button type="submit" class="btn-hero">Submit Review</button>
    </form>

    <hr>

    <!-- Reviews -->
    <h2>All Reviews</h2>
    <?php if (empty($reviews)): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <?php foreach ($reviews as $r): ?>
            <div class="review-card">
                <strong><?= htmlspecialchars($r['reviewer']) ?></strong>
                <p>⭐ <?= $r['rating'] ?>/5</p>
                <p><?= htmlspecialchars($r['reviewText']) ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
