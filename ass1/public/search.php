<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require 'includes/header.php';

$search = trim($_GET['search'] ?? '');
$results = [];

if ($search !== '') {
    $keyword = "%" . $search . "%";

    $stmt = $pdo->prepare("
        SELECT * FROM auction 
        WHERE title LIKE ? OR description LIKE ?
    ");
    $stmt->execute([$keyword, $keyword]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="search-container">

    <h2>Search Auctions</h2>

    <!-- SEARCH FORM -->
    <form method="GET" class="search-form">
        <input type="text" name="search"
               placeholder="Search cars, brands, auctions..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- RESULTS -->
    <div class="search-results">

        <?php if ($search === ''): ?>
            <p class="info">Enter a keyword to search auctions.</p>

        <?php elseif (empty($results)): ?>
            <p class="error">No results found for "<?= htmlspecialchars($search) ?>"</p>

        <?php else: ?>
            <p class="info">Found <?= count($results) ?> result(s)</p>

            <?php foreach ($results as $row): ?>
                <div class="result-card">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
        
    </div>

</div>
        <?php require 'includes/footer.php'; ?>
