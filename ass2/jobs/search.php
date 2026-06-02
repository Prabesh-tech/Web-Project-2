<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require 'includes/header.php';

$search = trim($_GET['search'] ?? '');
$results = [];

$availableTables = ['auction', 'jobs', 'job', 'listings'];
$jobTable = null;
foreach ($availableTables as $tableName) {
    if (tableExists($pdo, $tableName)) {
        $jobTable = $tableName;
        break;
    }
}

if ($search !== '' && $jobTable) {
    $keyword = "%" . $search . "%";

    $stmt = $pdo->prepare(
        "SELECT * FROM `$jobTable` WHERE title LIKE ? OR description LIKE ?"
    );
    $stmt->execute([$keyword, $keyword]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="search-container">

    <h2>Search Jobs</h2>

    <!-- SEARCH FORM -->
    <form method="GET" class="search-form">
        <input type="text" name="search"
               placeholder="Search job titles, keywords, or companies..."
               value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <!-- RESULTS -->
    <div class="search-results">

        <?php if ($search === ''): ?>
            <p class="info">Enter a keyword to search job listings.</p>

        <?php elseif (empty($results)): ?>
            <p class="error">No results found for "<?= htmlspecialchars($search) ?>"</p>

        <?php else: ?>
            <p class="info">Found <?= count($results) ?> job(s)</p>

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
