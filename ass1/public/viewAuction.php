<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

$auctionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$auctionId) {
	die("Invalid auction ID.");
}

$stmt = $pdo->prepare("SELECT a.*, c.name AS category, u.username AS owner
				   FROM auction a
				   LEFT JOIN categories c ON a.categoryId = c.id
				   LEFT JOIN users u ON a.userId = u.id
					   WHERE a.id = ?");
$stmt->execute([$auctionId]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
	die("Auction not found");
}

$bidError = '';
$bidSuccess = '';
$reviewError = '';
$reviewSuccess = '';

/* Handle Review Submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'review') {
	if (empty($_SESSION['user'])) {
		header('Location: login.php');
		exit;
	}

	$reviewText = trim($_POST['reviewText'] ?? '');
	if ($reviewText === '') {
		$reviewError = 'Please enter a review message.';
	} else {
		$stmt = $pdo->prepare("INSERT INTO reviews (userId, reviewerId, reviewText)
							   VALUES (?, ?, ?)");
		$stmt->execute([$auctionId, $_SESSION['user']['id'], $reviewText]);
		$reviewSuccess = 'Review submitted successfully.';
	}
}

/* Handle Bid Submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bid') {
	if (empty($_SESSION['user'])) {
		header('Location: login.php');
		exit;
	}

	$bid = trim($_POST['bid'] ?? '');
	
	// Validate and sanitize bid amount
	if (!is_numeric($bid) || $bid <= 0) {
		$bidError = 'Enter a valid bid amount.';
	} else {
		$bid = (float)$bid;
		
		// Check for reasonable bid limit (prevent overflow)
		if ($bid > 99999999.99) {
			$bidError = 'Bid amount is too high.';
		} else {
			$stmt = $pdo->prepare("SELECT MAX(amount) AS maxBid FROM bid WHERE auctionId = ?");
			$stmt->execute([$auctionId]);
			$currentMaxBid = (float)($stmt->fetchColumn() ?: 0);
			$minBid = max((float)$auction['currentBid'], $currentMaxBid);

			if ($bid <= $minBid) {
				$bidError = "Your bid must be higher than \$" . number_format($minBid, 2);
			} else {
				try {
					$stmt = $pdo->prepare("INSERT INTO bid (auctionId, userId, amount)
										   VALUES (?, ?, ?)");
					$stmt->execute([$auctionId, $_SESSION['user']['id'], number_format($bid, 2, '.', '')]);

					$stmt = $pdo->prepare("UPDATE auction SET currentBid = ? WHERE id = ?");
					$stmt->execute([number_format($bid, 2, '.', ''), $auctionId]);

					$bidSuccess = 'Your bid was placed successfully.';
				} catch (PDOException $e) {
					$bidError = 'Error placing bid: ' . $e->getMessage();
				}
			}
		}
	}
}

/* Handle Watch */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'watch') {
	if (empty($_SESSION['user'])) {
		header('Location: login.php');
		exit;
	}

	try {
		// Check if already watching
		$stmt = $pdo->prepare("SELECT id FROM watches WHERE auctionId = ? AND userId = ?");
		$stmt->execute([$auctionId, $_SESSION['user']['id']]);
		
		if ($stmt->rowCount() > 0) {
			// Remove watch
			$stmt = $pdo->prepare("DELETE FROM watches WHERE auctionId = ? AND userId = ?");
			$stmt->execute([$auctionId, $_SESSION['user']['id']]);
		} else {
			// Add watch
			$stmt = $pdo->prepare("INSERT INTO watches (auctionId, userId) VALUES (?, ?)");
			$stmt->execute([$auctionId, $_SESSION['user']['id']]);
		}
	} catch (PDOException $e) {
		// Silently fail - not critical
	}
}

/* Fetch Reviews */
$reviews = $pdo->prepare("SELECT r.*, u.username AS reviewer
								  FROM reviews r
						  JOIN users u ON r.reviewerId = u.id
						  WHERE r.userId = ?");
$reviews->execute([$auctionId]);
$reviews = $reviews->fetchAll(PDO::FETCH_ASSOC);

/* Fetch Highest Bid */
$highestBid = $pdo->prepare("SELECT MAX(amount) AS maxBid FROM bid WHERE auctionId = ?");
$highestBid->execute([$auctionId]);
$maxBid = $highestBid->fetchColumn();

/* Fetch Watch Count */
$watchCount = 0;
$isWatching = false;
try {
	$watchStmt = $pdo->prepare("SELECT COUNT(*) FROM watches WHERE auctionId = ?");
	$watchStmt->execute([$auctionId]);
	$watchCount = (int)$watchStmt->fetchColumn();

	/* Check if current user is watching */
	if (!empty($_SESSION['user'])) {
		$watchCheckStmt = $pdo->prepare("SELECT id FROM watches WHERE auctionId = ? AND userId = ?");
		$watchCheckStmt->execute([$auctionId, $_SESSION['user']['id']]);
		$isWatching = $watchCheckStmt->rowCount() > 0;
	}
} catch (PDOException $e) {
	// Watches table may not exist yet, silently fail
	$watchCount = 0;
	$isWatching = false;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title><?= htmlspecialchars($auction['title']) ?></title>
	<link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>

<header class="site-header">
	<div class="header-inner">
		<a href="index.php" class="logo">CarBuy</a>
		<div class="user-area">
			<?php if (!empty($_SESSION['user']['username'])): ?>
				<span class="username">Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
				<a href="logout.php" class="user-btn">Logout</a>
			<?php else: ?>
				<a href="login.php" class="auth-link">
					<span>Sign in</span>
					<span class="user-icon">👤</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</header>

<div class="auction-page">
	<a href="index.php" class="btn-back">← Back to Home</a>
	<h1><?= htmlspecialchars($auction['title']) ?></h1>
	<?php if ((!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) || (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin')): ?>
		<div class="auction-actions">
			<a href="editAuction.php?id=<?= $auctionId ?>" class="btn-admin">Edit Auction</a>

			<form method="POST" action="deleteAuction.php" onsubmit="return confirm('Delete this auction? This cannot be undone.');" style="display:inline-block;margin:0;">
				<input type="hidden" name="id" value="<?= $auctionId ?>">
				<button type="submit" class="btn-delete">Delete Auction</button>
			</form>
		</div>
	<?php endif; ?>
	<?php
	    $auctionImage = '';
	    if (!empty($auction['image'])) {
	        $imagePath = $auction['image'];
	        if (strpos($imagePath, 'images/auctions/') === 0) {
	            if (file_exists(__DIR__ . '/' . $imagePath)) {
	                $auctionImage = $imagePath;
	            }
	        } elseif (file_exists(__DIR__ . '/images/auctions/' . $imagePath)) {
	            $auctionImage = 'images/auctions/' . htmlspecialchars($imagePath);
	        } elseif (file_exists(__DIR__ . '/assets/images/' . $imagePath)) {
	            $auctionImage = 'assets/images/' . htmlspecialchars($imagePath);
	        }
	    }
	?>
	<?php if ($auctionImage): ?>
		<div class="auction-image">
			<a href="<?= $auctionImage ?>" target="_blank" rel="noopener noreferrer">
				<img src="<?= $auctionImage ?>" alt="Car Image">
			</a>
			<a href="<?= $auctionImage ?>" target="_blank" rel="noopener noreferrer" class="direct-image-link">View full image</a>
		</div>
	<?php else: ?>
		<div class="auction-image">
			<img src="assets/images/default-car.jpg" alt="Car Image">
		</div>
	<?php endif; ?>

	<p><?= nl2br(htmlspecialchars($auction['description'])) ?></p>
	<p><strong>Category:</strong> <?= htmlspecialchars($auction['category']) ?></p>
	<p><strong>Owner:</strong> <?= htmlspecialchars($auction['owner'] ?? 'Unknown') ?></p>
	<p><strong>Ends:</strong> <?= htmlspecialchars($auction['endDate']) ?></p>
	<p><strong>Highest Bid:</strong> <?= $maxBid ? number_format($maxBid, 2) : "No bids yet" ?></p>
	
	<div class="auction-meta-row">
		<div class="meta-watching">👁️ <?= $watchCount ?> watching</div>
		<?php if (!empty($_SESSION['user'])): ?>
			<form method="POST" style="display: inline;">
				<input type="hidden" name="action" value="watch">
				<button type="submit" class="btn-watch <?= $isWatching ? 'watching' : '' ?>">
					<?= $isWatching ? '★ Watching' : '☆ Watch' ?>
				</button>
			</form>
		<?php endif; ?>
	</div>

	<?php if (!empty($bidError)): ?>
		<div class="auth-error"><?= htmlspecialchars($bidError) ?></div>
	<?php endif; ?>
	<?php if (!empty($bidSuccess)): ?>
		<div class="auth-success"><?= htmlspecialchars($bidSuccess) ?></div>
	<?php endif; ?>
	<?php if (!empty($reviewError)): ?>
		<div class="auth-error"><?= htmlspecialchars($reviewError) ?></div>
	<?php endif; ?>
	<?php if (!empty($reviewSuccess)): ?>
		<div class="auth-success"><?= htmlspecialchars($reviewSuccess) ?></div>
	<?php endif; ?>

	<?php if (!empty($_SESSION['user'])): ?>
		<div class="bid-section">
			<div class="bid-panel">
				<h3>Place Your Bid</h3>
				<p class="min-bid-text">Minimum bid: $<?= number_format((float)max($auction['currentBid'], $maxBid ?: 0) + 1, 2) ?></p>
				
				<form method="POST" class="bid-form">
					<input type="hidden" name="action" value="bid">
					<div class="form-group">
						<label for="bid-input">Your Bid Amount</label>
						<div class="bid-input-wrapper">
							<span class="currency">$</span>
							<input type="number" id="bid-input" name="bid" step="0.01" placeholder="Enter amount" required>
						</div>
					</div>
					<button type="submit" class="btn-place-bid-submit">Place Bid</button>
				</form>
			</div>
		</div>

		<div class="review-section">
			<h3>Leave a Review</h3>
			<form method="POST" class="review-form">
				<input type="hidden" name="action" value="review">
				<div class="form-group">
					<textarea name="reviewText" placeholder="Share your thoughts about this seller..." required></textarea>
				</div>
				<button type="submit" class="btn-review-submit">Submit Review</button>
			</form>
		</div>
	<?php else: ?>
		<div class="login-prompt">
			<p><a href="login.php" class="btn-login-prompt">Login to bid or review</a></p>
		</div>
	<?php endif; ?>

	<div class="reviews-list">
		<h3>Reviews</h3>
		<?php if (!empty($reviews)): ?>
			<?php foreach ($reviews as $r): ?>
				<div class="review-item">
					<strong><?= htmlspecialchars($r['reviewer']) ?></strong>
					<p><?= htmlspecialchars($r['reviewText']) ?></p>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<p style="color: #999;">No reviews yet.</p>
		<?php endif; ?>
	</div>
	</div>
</div>

<footer class="site-footer">
	<p>&copy; <?= date('Y') ?> CarBuy</p>
</footer>

</body>
</html>

