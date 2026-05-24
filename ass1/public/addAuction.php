<?php
require_once __DIR__ . '/auth.php';

// Allow authenticated users to create auctions (normal users and admins)
$error = '';
$success = false;
$formAction = 'addAuction.php';

if (empty(currentUser()) || !isUserAllowedToAddAuction()) {
    if (isset($_GET['embedded'])) {
        http_response_code(403);
        echo "<div class='auth-error'>You must be a normal user or admin to add auctions.</div>";
        exit;
    }
    header('Location: login.php');
    exit;
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$title = '';
$description = '';
$year = '';
$mileage = '';
$currentBid = '';
$endDate = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = intval($_POST['categoryId'] ?? 0);
    $year = intval($_POST['year'] ?? 0);
    $mileage = intval($_POST['mileage'] ?? 0);
    $currentBid = floatval($_POST['currentBid'] ?? 0);
    $endDate = trim($_POST['endDate'] ?? '');

    if ($categoryId <= 0) {
        $error = 'Please select a valid category.';
    }

    if (empty($categories)) {
        $error = 'Please create at least one category before adding an auction.';
    }

    if ($endDate !== '') {
        $date = DateTime::createFromFormat('Y-m-d\TH:i', $endDate);
        if ($date === false) {
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $endDate);
        }
        if ($date === false) {
            $error = 'Please enter a valid end date and time.';
        } else {
            $endDate = $date->format('Y-m-d H:i:s');
        }
    } else {
        $error = 'An auction end date is required.';
    }

    // Handle image upload (hardened)
    $imageName = '';
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
            // enforce max size (20MB)
            $maxBytes = 20 * 1024 * 1024;
            if (isset($_FILES['image']['size']) && $_FILES['image']['size'] > $maxBytes) {
                $error = 'Uploaded file exceeds maximum allowed size of 20MB.';
            } else {
                $targetDir = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'auctions' . DIRECTORY_SEPARATOR;
                if (!is_dir($targetDir)) {
                    if (!@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                        $error = "Unable to create upload folder.";
                    }
                }
                if ($error === '') {
                    $originalName = basename($_FILES['image']['name']);
                    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','gif','webp'];
                    if (!in_array($ext, $allowed, true)) {
                        $error = 'Invalid image type. Allowed: jpg,jpeg,png,gif,webp.';
                    } else {
                        // sanitize base name
                        $base = pathinfo($originalName, PATHINFO_FILENAME);
                        $base = preg_replace('/[^A-Za-z0-9_-]/', '_', $base);
                        $imageName = time() . '_' . $base . '.' . $ext;
                        $targetFile = $targetDir . $imageName;
                        if (!@move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                            $error = "Failed to move uploaded image. Please try again.";
                            $imageName = '';
                        }
                    }
                }
            }
        }
    }

    if ($error === '') {
        $userId = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;
        if ($userId <= 0) {
            $error = 'Unable to determine your user account. Please log in again and try.';
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO auction (title, description, categoryId, userId, image, year, mileage, currentBid, endDate)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $categoryId, $userId, $imageName, $year, $mileage, $currentBid, $endDate]);
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = "DB Error: " . $e->getMessage();
            }
        }
    }
}
// If requested to embed only the form (modal), return form fragment
if (isset($_GET['embedded'])) {
    include __DIR__ . '/includes/addAuction_form.php';
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Auction</title>
<link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>
<div class="admin-page">
    <div class="admin-card">
        <h2>Add New Car Auction</h2>

        <?php include __DIR__ . '/includes/addAuction_form.php'; ?>

        <a href="index.php" class="admin-back">← Back to Home</a>
    </div>
</div>
</body>
</html>
