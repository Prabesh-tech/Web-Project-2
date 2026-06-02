<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

header('Content-Type: application/json');

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? 'All';
$brand = $_GET['brand'] ?? '';

if (strlen($search) < 2) {
    echo json_encode(['success' => true, 'jobs' => []]);
    exit;
}

$availableTables = ['auction', 'jobs', 'job', 'listings'];
$jobTable = null;
foreach ($availableTables as $tableName) {
    if (tableExists($pdo, $tableName)) {
        $jobTable = $tableName;
        break;
    }
}

$categoriesAvailable = tableExists($pdo, 'categories');
$usersAvailable = tableExists($pdo, 'users');

if (!$jobTable) {
    echo json_encode(['success' => true, 'auctions' => []]);
    exit;
}

$query = 'SELECT a.*';
if ($categoriesAvailable) {
    $query .= ', c.name AS category';
}
if ($usersAvailable) {
    $query .= ', u.username AS owner';
}
$query .= " FROM `$jobTable` a";

if ($categoriesAvailable) {
    $query .= ' LEFT JOIN categories c ON a.categoryId = c.id';
}
if ($usersAvailable) {
    $query .= ' LEFT JOIN users u ON a.postedBy = u.id';
}

$params = [];
$conditions = [];

if ($category !== 'All' && $categoriesAvailable) {
    $conditions[] = 'c.name = ?';
    $params[] = $category;
}

if ($brand !== '') {
    $brandSearch = "%$brand%";
    $conditions[] = '(a.title LIKE ? OR a.description LIKE ?' . ($categoriesAvailable ? ' OR c.name LIKE ?' : '') . ($usersAvailable ? ' OR u.username LIKE ?' : '') . ')';
    $params[] = $brandSearch;
    $params[] = $brandSearch;
    if ($categoriesAvailable) {
        $params[] = $brandSearch;
    }
    if ($usersAvailable) {
        $params[] = $brandSearch;
    }
}

$searchConditions = ['a.title LIKE ?', 'a.description LIKE ?'];
$params[] = "%$search%";
$params[] = "%$search%";
if ($categoriesAvailable) {
    $searchConditions[] = 'c.name LIKE ?';
    $params[] = "%$search%";
}
if ($usersAvailable) {
    $searchConditions[] = 'u.username LIKE ?';
    $params[] = "%$search%";
}
$conditions[] = '(' . implode(' OR ', $searchConditions) . ')';

if ($conditions) {
    $query .= ' WHERE ' . implode(' AND ', $conditions);
}
$query .= ' ORDER BY a.closingDate ASC LIMIT 20';

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $auctions = $stmt->fetchAll();

    $countBidStmt = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE jobId = ?');
    try {
        $countWatchStmt = $pdo->prepare('SELECT COUNT(*) FROM saved_jobs WHERE jobId = ?');
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
            $imagePath = $a['image'];
            if (strpos($imagePath, 'images/auctions/') === 0) {
                if (!file_exists(__DIR__ . '/' . $imagePath)) {
                    $imagePath = null;
                }
            } elseif (file_exists(__DIR__ . '/images/auctions/' . $imagePath)) {
                $imagePath = 'images/auctions/' . $imagePath;
            } elseif (file_exists(__DIR__ . '/assets/images/' . $imagePath)) {
                $imagePath = 'assets/images/' . $imagePath;
            } else {
                $imagePath = null;
            }
            $a['image'] = $imagePath;
        }
    }

    echo json_encode(['success' => true, 'jobs' => $auctions]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => htmlspecialchars($e->getMessage())]);
}
?>
