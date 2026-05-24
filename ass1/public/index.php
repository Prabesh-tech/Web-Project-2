<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

// Active category, brand & search
$activeCat = $_GET['category'] ?? 'All';
$activeBrand = $_GET['brand'] ?? '';
$search = trim($_GET['search'] ?? '');

// Do not override explicit searches with brand; keep brand as separate filter

//  Fetch auctions (10 finishing soonest)
$query = "SELECT a.*, c.name AS category, u.username AS owner
          FROM auction a
          LEFT JOIN categories c ON a.categoryId = c.id
          LEFT JOIN users u ON a.userId = u.id";

$params = [];
$conditions = [];

// Filter by category
if ($activeCat !== 'All') {
    $conditions[] = "c.name = ?";
    $params[] = $activeCat;
}

if ($activeBrand !== '') {
    // Filter auctions by brand keyword appearing in title or description
    $conditions[] = "(a.title LIKE ? OR a.description LIKE ?)";
    $params[] = "%$activeBrand%";
    $params[] = "%$activeBrand%";
}

// Search filter
if ($search !== '') {
    $conditions[] = "(a.title LIKE ? OR a.description LIKE ? OR c.name LIKE ? OR u.username LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($conditions) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}
$query .= " ORDER BY a.endDate ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CarBuy – Car Auction</title>
<link rel="stylesheet" href="assets/carbuy.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="index.php" class="logo">CarBuy</a>

        <form class="search-form" id="searchForm">
            <input type="hidden" name="brand" id="brandInput" value="<?= htmlspecialchars($activeBrand) ?>">
            <input type="text" name="search" id="searchInput" class="search-input"
                   placeholder="Search for a car"
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn-search">Search</button>
        </form>

        <!--  Keep Sign up icon and text -->
        <div class="user-area">
                <?php if (!empty($_SESSION['user']['username'])):
                    $isAdminUser = (!empty($_SESSION['user']['isAdmin']) && in_array(intval($_SESSION['user']['isAdmin']), [1,2], true)) ||
                                   (!empty($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'Super Admin');
                ?>
                    <?php if ($isAdminUser): ?>
                        <a href="admin.php" class="btn-addauction admin-dashboard">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="addAuction.php" class="btn-addauction">Add Auction</a>
                    <?php endif; ?>
                    <span class="username">Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                    <a href="logout.php" class="user-btn">Logout</a>
                <?php else: ?>
                <a href="login.php" class="auth-link">
                    <span>Sign up</span>
                    <span class="user-icon">👤</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="brand-bar">
    <div class="brand-bar-inner">
        <span class="brand-label">Brands</span>
        <a href="index.php#car-section" class="brand-chip <?= $activeBrand === '' ? 'active' : '' ?>">All</a>
        <?php
        // Fetch top brands from database
        $topBrandsStmt = $pdo->query('SELECT name FROM brands WHERE isTopBrand = 1 ORDER BY name ASC');
        $topBrands = $topBrandsStmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($topBrands as &$topBrand) {
            if ($topBrand === 'Rolls Royce') {
                $topBrand = 'Rolls Royace';
            }
        }
        unset($topBrand);
        
        // Fetch more brands from database
        $moreBrandsStmt = $pdo->query('SELECT name FROM brands WHERE isTopBrand = 0 ORDER BY name ASC');
        $moreBrands = $moreBrandsStmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($topBrands as $brand):
        ?>
            <a href="index.php?brand=<?= urlencode($brand) ?>#car-section" class="brand-chip <?= $activeBrand === $brand ? 'active' : '' ?>">
                <?= htmlspecialchars($brand) ?>
            </a>
        <?php endforeach; ?>

        <div class="brand-chip brand-more <?= in_array($activeBrand, $moreBrands, true) ? 'active' : '' ?>">
            More
            <div class="brand-dropdown">
                <?php foreach ($moreBrands as $brand): ?>
                    <a href="index.php?brand=<?= urlencode($brand) ?>#car-section" class="brand-dropdown-item <?= $activeBrand === $brand ? 'active' : '' ?>">
                        <?= htmlspecialchars($brand) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="hero">
    <div class="hero-inner">
        <div class="hero-img-wrap">
            <img src="assets/images/car2.jpg" class="hero-img" alt="Hero Car">
        </div>
    </div>
</section>

<main id="car-section" class="listings">
    <div class="listings-inner">
        <h2 class="listings-heading">Latest Car Auctions</h2>

        <?php if (empty($auctions)): ?>
            <div class="no-results">No cars yet. Add auctions to see them here.</div>
        <?php else: ?>
            <div class="car-grid">
                <?php
                $countBidStmt = $pdo->prepare("SELECT COUNT(*) FROM bid WHERE auctionId = ?");
                
                // Prepare watch count with error handling (in case table doesn't exist)
                try {
                    $countWatchStmt = $pdo->prepare("SELECT COUNT(*) FROM watches WHERE auctionId = ?");
                } catch (PDOException $e) {
                    $countWatchStmt = null;
                }
                
                foreach ($auctions as $a):
                    $countBidStmt->execute([$a['id']]);
                    $bidsCount = (int)$countBidStmt->fetchColumn();
                    
                    $watchCount = 0;
                    if ($countWatchStmt) {
                        try {
                            $countWatchStmt->execute([$a['id']]);
                            $watchCount = (int)$countWatchStmt->fetchColumn();
                        } catch (PDOException $e) {
                            $watchCount = 0;
                        }
                    }
                ?>
                    <div class="car-card">
                        <div class="card-thumb">
                            <?php
                                $auctionImage = '';
                                if (!empty($a['image'])) {
                                    $imagePath = $a['image'];
                                    if (strpos($imagePath, 'images/auctions/') === 0) {
                                        if (file_exists(__DIR__ . '/' . $imagePath)) {
                                            $auctionImage = htmlspecialchars($imagePath);
                                        }
                                    } elseif (file_exists(__DIR__ . '/images/auctions/' . $imagePath)) {
                                        $auctionImage = 'images/auctions/' . htmlspecialchars($imagePath);
                                    } elseif (file_exists(__DIR__ . '/assets/images/' . $imagePath)) {
                                        $auctionImage = 'assets/images/' . htmlspecialchars($imagePath);
                                    }
                                }
                            ?>
                            <?php if ($auctionImage): ?>
                                <a href="<?= $auctionImage ?>" target="_blank" rel="noopener noreferrer" class="image-link">
                                    <img src="<?= $auctionImage ?>" alt="Car Image">
                                </a>
                            <?php else: ?>
                                <img src="assets/images/default-car.jpg" alt="Default Car">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="card-badges">
                                <span class="badge reserve">Reserve Met</span>
                                <span class="badge time"><?php
                                    $timeRemaining = '—';
                                    try {
                                        if (!empty($a['endDate'])) {
                                            $now = new DateTime();
                                            $end = new DateTime($a['endDate']);
                                            $diff = $now->diff($end);
                                            $timeRemaining = ($diff->invert === 0) ? $diff->days . 'd ' . $diff->h . 'h' : 'Ended';
                                        }
                                    } catch (Exception $e) {
                                        $timeRemaining = '—';
                                    }
                                    echo htmlspecialchars($timeRemaining);
                                ?></span>
                            </div>

                            <h3><?= htmlspecialchars($a['title']) ?></h3>
                            <p class="card-desc"><?= htmlspecialchars($a['year']) ?> · <?= htmlspecialchars($a['mileage']) ?> miles</p>
                            <p class="card-owner">Owner: <?= htmlspecialchars($a['owner'] ?? 'Unknown') ?></p>

                            <div class="bid-row">
                                <div class="bid-label">Current Bid</div>
                                <div class="bid-price">$<?= number_format((float)$a['currentBid'], 2) ?></div>
                            </div>

                            <div class="meta-row">
                                <div class="meta-item">🔨 <?= $bidsCount ?> bids</div>
                                <div class="meta-item">👁️ <?= $watchCount ?> watching</div>
                            </div>

                            <?php if (!empty($_SESSION['user']['username'])): ?>
                                <a href="viewAuction.php?id=<?= $a['id'] ?>" class="btn place-bid">Place Bid</a>
                                <a href="viewAuction.php?id=<?= $a['id'] ?>" class="btn watch-btn">Watch Auction</a>
                            <?php else: ?>
                                <?php $returnUrl = urlencode('viewAuction.php?id=' . $a['id']); ?>
                                <a href="login.php?redirect=<?= $returnUrl ?>" class="btn place-bid">Login to bid</a>
                                <a href="login.php?redirect=<?= $returnUrl ?>" class="btn watch-btn">Login to watch</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<div id="searchResults" class="search-results-modal" style="display: none;">
    <div class="search-results-overlay" onclick="closeSearchResults()"></div>
    <div class="search-results-container">
        <button class="search-close-btn" onclick="closeSearchResults()">✕</button>
        <div id="searchResultsContent"></div>
    </div>
</div>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> CarBuy</p>
</footer>

<script>
const searchInput = document.getElementById('searchInput');
const searchForm = document.getElementById('searchForm');
const searchResults = document.getElementById('searchResults');
const searchResultsContent = document.getElementById('searchResultsContent');

let searchTimeout;

// Live search as user types
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    const query = this.value.trim();
    
    if (query.length < 2) {
        closeSearchResults();
        return;
    }
    
    // Debounce search requests
    searchTimeout = setTimeout(() => {
        performSearch(query);
    }, 300);
});

function performSearch(query) {
    const url = `search-api.php?search=${encodeURIComponent(query)}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.auctions.length > 0) {
                displaySearchResults(data.auctions);
                searchResults.style.display = 'flex';
            } else {
                searchResultsContent.innerHTML = '<div class="no-results">No auctions found</div>';
                searchResults.style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResultsContent.innerHTML = '<div class="auth-error">Search failed</div>';
            searchResults.style.display = 'flex';
        });
}

function displaySearchResults(auctions) {
    let html = '<h3 class="search-results-title">Search Results</h3>';
    html += '<div class="search-results-grid">';
    
    auctions.forEach(auction => {
        const imageUrl = auction.image
        ? (auction.image.startsWith('assets/') ? auction.image : `images/auctions/${auction.image}`)
        : 'assets/images/default-car.jpg';
        const bidPrice = parseFloat(auction.currentBid).toFixed(2);
        
        html += `
            <a href="viewAuction.php?id=${auction.id}" class="search-result-card">
                <div class="search-result-thumb">
                    <img src="${imageUrl}" alt="Car Image">
                </div>
                <div class="search-result-body">
                    <h4>${auction.title}</h4>
                    <p class="search-result-category">${auction.category}</p>
                    <div class="search-result-bid">$${bidPrice}</div>
                    <div class="search-result-meta">👁️ ${auction.watchCount} watching</div>
                </div>
            </a>
        `;
    });
    
    html += '</div>';
    searchResultsContent.innerHTML = html;
}

function closeSearchResults() {
    searchResults.style.display = 'none';
}

// Close search results when clicking on a result
document.addEventListener('click', function(event) {
    if (!event.target.closest('#searchResults') && !event.target.closest('#searchInput')) {
        closeSearchResults();
    }
});

// Prevent form submission on Enter (let live search handle it)
searchForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const query = searchInput.value.trim();
    if (query.length > 0) {
        const brand = document.getElementById('brandInput') ? document.getElementById('brandInput').value : '';
        let url = `index.php?search=${encodeURIComponent(query)}`;
        if (brand && brand.length > 0) {
            url += `&brand=${encodeURIComponent(brand)}`;
        }
        // Jump to car listings after search
        url += '#car-section';
        window.location.href = url;
    }
});
</script>

</body>
</html>
