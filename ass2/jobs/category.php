<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

$categoryId = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT name, description, image FROM categories WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

$categoryName = $category['name'] ?? 'Category';
$categoryDescription = $category['description'] ?? '';
$categoryImage = '';
if (!empty($category['image'])) {
    $imageFile = $category['image'];
    if (strpos($imageFile, 'assets/') === 0) {
        $categoryImage = $imageFile;
    } elseif (file_exists(__DIR__ . '/assets/images/' . $imageFile)) {
        $categoryImage = 'assets/images/' . htmlspecialchars($imageFile);
    } elseif (file_exists(__DIR__ . '/images/auctions/' . $imageFile)) {
        $categoryImage = 'images/auctions/' . htmlspecialchars($imageFile);
    }
}
if ($categoryImage === '') {
    $categoryImage = 'assets/images/image1.jpg';
}

$auctions = [];
if ($categoryId && $categoryName !== 'Category') {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE categoryId = ?");
    $stmt->execute([$categoryId]);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($categoryName) ?> Jobs</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="category-header">
    <div class="category-image-wrap">
        <img src="<?= $categoryImage ?>" alt="<?= htmlspecialchars($categoryName) ?>" class="category-image">
    </div>
    <div class="category-text">
        <h1><?= htmlspecialchars($categoryName) ?> Jobs</h1>
        <?php if ($categoryDescription !== ''): ?>
            <p><?= htmlspecialchars($categoryDescription) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php foreach ($auctions as $a): ?>
    <div>
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <p><?= htmlspecialchars($a['description']) ?></p>
        <a class="more auctionLink" href="jobs/job.php?id=<?= $a['id'] ?>">More</a>
    </div>
<?php endforeach; ?>
</body>
</html>
