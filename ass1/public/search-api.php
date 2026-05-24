<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

header('Content-Type: application/json');

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? 'All';
$brand = $_GET['brand'] ?? '';

// Require minimum 2 characters for search
if (strlen($search) < 2) {
    echo json_encode(['success' => true, 'auctions' => []]);
    exit;
}

$query = "SELECT a.*, c.name AS category, u.username AS owner
          FROM auction a
          LEFT JOIN categories c ON a.categoryId = c.id
          LEFT JOIN users u ON a.userId = u.id";

$params = [];
$conditions = [];

// Filter by category
if ($category !== 'All') {
    $conditions[] = "c.name = ?";
    $params[] = $category;
}

// Filter by brand
if ($brand !== '') {
    $conditions[] = "(a.title LIKE ? OR a.description LIKE ? OR c.name LIKE ? OR u.username LIKE ?)";
    $params[] = "%$brand%";
    $params[] = "%$brand%";
    $params[] = "%$brand%";
    $params[] = "%$brand%";
}

// Search filter
$conditions[] = "(a.title LIKE ? OR a.description LIKE ? OR c.name LIKE ? OR u.username LIKE ?)";
$params[] = "%$search%";
$params[] = "%$search%";
$params[] = "%$search%";
$params[] = "%$search%";

if ($conditions) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY a.endDate ASC LIMIT 20";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get bid and watch counts with error handling
    $countBidStmt = $pdo->prepare("SELECT COUNT(*) FROM bid WHERE auctionId = ?");
    
    try {
        $countWatchStmt = $pdo->prepare("SELECT COUNT(*) FROM watches WHERE auctionId = ?");
    } catch (PDOException $e) {
        $countWatchStmt = null;
    }
    
    foreach ($auctions as &$a) {
        $countBidStmt->execute([$a['id']]);
        $a['bidsCount'] = (int)$countBidStmt->fetchColumn();
        
        $a['watchCount'] = 0;
        if ($countWatchStmt) {
            try {
                $countWatchStmt->execute([$a['id']]);
                $a['watchCount'] = (int)$countWatchStmt->fetchColumn();
            } catch (PDOException $e) {
                $a['watchCount'] = 0;
            }
        }

        if (!empty($a['image'])) {
            if (file_exists(__DIR__ . '/images/auctions/' . $a['image'])) {
                // keep path as-is for client-side rendering
            } elseif (file_exists(__DIR__ . '/assets/images/' . $a['image'])) {
                $a['image'] = 'assets/images/' . $a['image'];
            } else {
                $a['image'] = null;
            }
        }
    }

    echo json_encode(['success' => true, 'auctions' => $auctions]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
