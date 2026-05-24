<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

$auctionId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$auctionId) {
    die("Invalid auction ID.");
}

$stmt = $pdo->prepare("SELECT a.*, c.name AS category
                       FROM auction a
                       LEFT JOIN categories c ON a.categoryId = c.id
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
        $stmt = $pdo->prepare("INSERT INTO review (userId, reviewerId, reviewText)
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

    $bid = (float)($_POST['bid'] ?? 0);
    $stmt = $pdo->prepare("SELECT MAX(amount) AS maxBid FROM bid WHERE auctionId = ?");
    $stmt->execute([$auctionId]);
    $currentMaxBid = (float)($stmt->fetchColumn() ?: 0);
    $minBid = max((float)$auction['currentBid'], $currentMaxBid);

    if ($bid <= 0) {
        $bidError = 'Enter a valid bid amount.';
    } elseif ($bid <= $minBid) {
        $bidError = 'Your bid must be higher than the current bid.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO bid (auctionId, userId, amount)
                               VALUES (?, ?, ?)");
        $stmt->execute([$auctionId, $_SESSION['user']['id'], $bid]);

        $stmt = $pdo->prepare("UPDATE auction SET currentBid = ? WHERE id = ?");
        $stmt->execute([$bid, $auctionId]);

        $bidSuccess = 'Your bid was placed successfully.';
        $currentMaxBid = $bid;
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

$addAuctionError = '';
$addAuctionSuccess = false;
$formAction = 'auction.php?id=' . $auctionId;
$formType = 'addAuction';

$userRole = $_SESSION['user']['role'] ?? null;
if (empty($userRole) && isset($_SESSION['user']['isAdmin'])) {
    switch (intval($_SESSION['user']['isAdmin'])) {
        case 2:
            $userRole = 'Super Admin';
            break;
        case 1:
            $userRole = 'Admin';
            break;
        default:
            $userRole = 'User';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['formType'] ?? '') === 'addAuction') {
    if (empty($_SESSION['user']) || $userRole !== 'User') {
        $addAuctionError = 'Only normal users can add auctions.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = intval($_POST['categoryId'] ?? 0);
        $year = intval($_POST['year'] ?? 0);
        $mileage = intval($_POST['mileage'] ?? 0);
        $currentBid = floatval($_POST['currentBid'] ?? 0);
        $endDate = $_POST['endDate'] ?? '';

        $imageName = '';
        if (!empty($_FILES['image']['name'])) {
            $uploadError = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
            if ($uploadError !== UPLOAD_ERR_OK) {
                switch ($uploadError) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        $addAuctionError = "Uploaded file is too large.";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $addAuctionError = "File upload was interrupted. Please try again.";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $addAuctionError = "No image file was uploaded.";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $addAuctionError = "Server is missing a temporary folder.";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $addAuctionError = "Server failed to write the uploaded file.";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $addAuctionError = "A PHP extension stopped the file upload.";
                        break;
                    default:
                        $addAuctionError = "Failed to upload image. Please try again.";
                }
            } elseif (!is_uploaded_file($_FILES['image']['tmp_name'])) {
                $addAuctionError = "Invalid upload file. Please try again.";
            } else {
                $targetDir = __DIR__ . "/images/auctions/";
                if (!is_dir($targetDir)) {
                    $parentDir = dirname($targetDir);
                    if (!is_dir($parentDir) && !mkdir($parentDir, 0777, true)) {
                        $addAuctionError = "Unable to create upload folder.";
                    } elseif (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
                        $addAuctionError = "Unable to create upload folder.";
                    }
                }
                if ($addAuctionError === '') {
                    $imageName = time() . "_" . basename($_FILES['image']['name']);
                    $targetFile = $targetDir . $imageName;
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                        $addAuctionError = "Failed to move uploaded image. Please try again.";
                        $imageName = '';
                    }
                }
            }
        }

        if ($addAuctionError === '') {
            $userId = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
            if ($userId <= 0) {
                $addAuctionError = 'Unable to determine your user account. Please log in again and try.';
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO auction (title, description, categoryId, userId, image, year, mileage, currentBid, endDate)
                                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $description, $categoryId, $userId, $imageName, $year, $mileage, $currentBid, $endDate]);
                    header('Location: index.php');
                    exit;
                } catch (PDOException $e) {
                    $addAuctionError = "DB Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($auction['title']) ?></title></head>
<body>
<a href="index.php" class="btn-back">← Back to Home</a>
<h1><?= htmlspecialchars($auction['title']) ?></h1>
<p><?= htmlspecialchars($auction['description']) ?></p>
<p>Category: <?= htmlspecialchars($auction['category']) ?></p>
<p>Auction ends: <?= htmlspecialchars($auction['endDate']) ?></p>
<p>Highest Bid: <?= $maxBid ? number_format($maxBid, 2) : "No bids yet" ?></p>

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

<?php if (!empty($_SESSION['user']) && $userRole === 'User'): ?>
    <h3>Add New Car Auction</h3>
    <?php if ($addAuctionSuccess): ?>
        <div class="auth-success">✅ Auction added successfully!</div>
    <?php elseif ($addAuctionError): ?>
        <div class="auth-error"><?= htmlspecialchars($addAuctionError) ?></div>
    <?php endif; ?>
    <?php include __DIR__ . '/includes/addAuction_form.php'; ?>
<?php endif; ?>

<?php if (!empty($_SESSION['user'])): ?>
    <h3>Place a Bid</h3>
    <form method="POST">
        <input type="hidden" name="action" value="bid">
        <input type="number" step="0.01" name="bid" required>
        <button type="submit">Bid</button>
    </form>

    <h3>Leave a Review</h3>
    <form method="POST">
        <input type="hidden" name="action" value="review">
        <textarea name="reviewText" required></textarea>
        <button type="submit">Submit Review</button>
    </form>
<?php else: ?>
    <p><a href="login.php">Login to bid or review</a></p>
<?php endif; ?>

<h3>Reviews</h3>
<?php foreach ($reviews as $r): ?>
    <p><strong><?= htmlspecialchars($r['reviewer']) ?>:</strong> <?= htmlspecialchars($r['reviewText']) ?></p>
<?php endforeach; ?>
</body>
</html>
