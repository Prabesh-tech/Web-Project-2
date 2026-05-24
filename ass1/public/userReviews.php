<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    header("Location: login.php");
    exit;
}

// ✅ Validate auction ID
if (!isset($_GET['auction_id'])) {
    die("Invalid request");
}

$auctionId = (int) $_GET['auction_id'];
$userId = $_SESSION['user']['id'];

$error = '';

// ✅ Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $comment = trim($_POST['reviewText']);
    $rating = (int) $_POST['rating'];

    if (!empty($comment) && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO reviews (userId, reviewerId, reviewText, rating) VALUES (?, ?, ?, ?)");
        $stmt->execute([$auctionId, $userId, $comment, $rating]);

        header("Location: userReviews.php?auction_id=$auctionId&success=1");
        exit;
    } else {
        $error = "Please enter a valid review and rating (1–5).";
    }
}

// ✅ Fetch reviews
$stmt = $pdo->prepare("
    SELECT r.*, u.username AS reviewer
    FROM reviews r 
    JOIN users u ON r.reviewerId = u.id
    WHERE r.userId = ?
    ORDER BY r.id DESC
");
$stmt->execute([$auctionId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'includes/header.php';
?>

<div class="page-section">
    <h1>User Reviews</h1>
    <a href="auction.php?id=<?= $auctionId ?>" class="btn-back">Back</a>

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
