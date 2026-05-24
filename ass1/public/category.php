<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

$categoryId = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
$stmt->execute([$categoryId]);
$category = $stmt->fetchColumn();

$auctions = [];
if ($categoryId) {
    $stmt = $pdo->prepare("SELECT * FROM auction WHERE categoryId = ?");
    $stmt->execute([$categoryId]);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head><title><?= htmlspecialchars($category) ?> Cars</title></head>
<body>
<h1><?= htmlspecialchars($category) ?> Cars</h1>
<?php foreach ($auctions as $a): ?>
    <div>
        <h3><?= htmlspecialchars($a['title']) ?></h3>
        <p><?= htmlspecialchars($a['description']) ?></p>
        <a class="more auctionLink" href="auction.php?id=<?= $a['id'] ?>">More</a>
    </div>
<?php endforeach; ?>
</body>
</html>
