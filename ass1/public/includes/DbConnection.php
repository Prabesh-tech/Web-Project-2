<?php
$host = getenv('DB_HOST') ?: 'mysql';
$dbname = getenv('DB_NAME') ?: 'Assignments';
$username = getenv('DB_USER') ?: 'user';
$password = getenv('DB_PASS') ?: 'password';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo 'Connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}

// Ensure required schema exists (development convenience): if `auction` or `brands` table missing, import Data.sql
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'auction'");
    $hasAuction = ($stmt && $stmt->fetch());
} catch (Exception $e) {
    $hasAuction = false;
}

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'brands'");
    $hasBrands = ($stmt && $stmt->fetch());
} catch (Exception $e) {
    $hasBrands = false;
}

if (!$hasAuction || !$hasBrands) {
    $sqlFile = __DIR__ . '/../Data.sql';
    if (file_exists($sqlFile) && is_readable($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        // Split statements on semicolon followed by a newline to avoid breaking definitions
        $parts = preg_split('/;\s*\n/', $sql);
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;
            try {
                $pdo->exec($part);
            } catch (PDOException $e) {
                // Ignore individual statement errors but continue — helpful during iterative development
            }
        }
    }
}
