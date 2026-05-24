<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

//  Restrict to admins or Super Admin (isAdmin 1 or 2)
if (!((!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
    (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin'))) {
    header("Location: login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$error = '';
$success = false;

// Fetch existing auction
$stmt = $pdo->prepare("SELECT * FROM auction WHERE id = ?");
$stmt->execute([$id]);
$auction = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$auction) {
    die("Auction not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryId = intval($_POST['categoryId']);
    $year = intval($_POST['year']);
    $mileage = intval($_POST['mileage']);
    $currentBid = floatval($_POST['currentBid']);
    $endDate = $_POST['endDate'];

    // ✅ Handle image upload (optional)
    $imageName = $auction['image']; // keep old image
    if (!empty($_FILES['image']['name'])) {
        $uploadError = $_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($uploadError !== UPLOAD_ERR_OK) {
            switch ($uploadError) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = "Uploaded file is too large.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error = "File upload was interrupted. Please try again.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error = "No image file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = "Server is missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error = "Server failed to write the uploaded file.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $error = "A PHP extension stopped the file upload.";
                    break;
                default:
                    $error = "Failed to upload image. Please try again.";
            }
        } elseif (!is_uploaded_file($_FILES['image']['tmp_name'])) {
            $error = "Invalid upload file. Please try again.";
        } else {
            $targetDir = __DIR__ . "/images/auctions/";
            if (!is_dir($targetDir)) {
                $parentDir = dirname($targetDir);
                if (!is_dir($parentDir) && !mkdir($parentDir, 0777, true)) {
                    $error = "Unable to create upload folder.";
                } elseif (!mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
                    $error = "Unable to create upload folder.";
                }
            }
            if ($error === '') {
                $imageName = time() . "_" . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $imageName;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $error = "Failed to move uploaded image. Please try again.";
                    $imageName = $auction['image'];
                }
            }
        }
    }

    if ($error === '') {
        try {
            $stmt = $pdo->prepare("UPDATE auction 
                                   SET title=?, description=?, categoryId=?, image=?, year=?, mileage=?, currentBid=?, endDate=? 
                                   WHERE id=?");
            $stmt->execute([$title, $description, $categoryId, $imageName, $year, $mileage, $currentBid, $endDate, $id]);
            $success = true;
            // Refresh auction data
            $stmt = $pdo->prepare("SELECT * FROM auction WHERE id = ?");
            $stmt->execute([$id]);
            $auction = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "DB Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Auction</title>
<link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-card">
        <h1>Edit Car Auction</h1>
        <a href="index.php" class="btn-back">← Back to index page</a>

        <?php if ($success): ?>
            <div class="auth-success">✅ Auction updated successfully!</div>
        <?php elseif ($error): ?>
            <div class="auth-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="auth-form">
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($auction['title']) ?>" required>

            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($auction['description']) ?></textarea>

            <label>Category</label>
            <select name="categoryId" required>
                <?php
                $cats = $pdo->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($cats as $c) {
                    $selected = $c['id'] == $auction['categoryId'] ? 'selected' : '';
                    echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                }
                ?>
            </select>

            <label>Year</label>
            <input type="number" name="year" value="<?= htmlspecialchars($auction['year']) ?>" required>

            <label>Mileage</label>
            <input type="number" name="mileage" value="<?= htmlspecialchars($auction['mileage']) ?>" required>

            <label>Current Bid ($)</label>
            <input type="number" step="0.01" name="currentBid" value="<?= htmlspecialchars($auction['currentBid']) ?>" required>

            <label>End Date</label>
            <input type="datetime-local" name="endDate" value="<?= date('Y-m-d\TH:i', strtotime($auction['endDate'])) ?>" required>

            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <div class="field-note">Maximum upload size: 20MB</div>
            <?php if (!empty($auction['image'])): ?>
                <?php
                    $currentImageUrl = $auction['image'];
                    if (strpos($currentImageUrl, 'images/auctions/') !== 0) {
                        $currentImageUrl = 'images/auctions/' . $currentImageUrl;
                    }
                ?>
                <p>Current:
                    <a href="<?= htmlspecialchars($currentImageUrl) ?>" target="_blank" rel="noopener noreferrer">
                        <img src="<?= htmlspecialchars($currentImageUrl) ?>" width="120" alt="Current auction image">
                    </a>
                </p>
            <?php endif; ?>

            <button type="submit" class="btn-login">Update Auction</button>
        </form>
    </div>
</div>
</body>
</html>
