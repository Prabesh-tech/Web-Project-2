<?php
$driver = getenv('DB_DRIVER') ?: 'mysql';

if ($driver === 'sqlite') {
    $dbName = getenv('DB_NAME') ?: ':memory:';
    $dsn = 'sqlite:' . $dbName;
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} else {
    $host = getenv('DB_HOST') ?: 'mysql';
    $dbname = getenv('DB_NAME') ?: 'Assignment2';
    $username = getenv('DB_USER') ?: 'user';
    $password = getenv('DB_PASS') ?: 'password';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        // When running in CLI or in explicit testing mode, rethrow so tests
        // (and other CLI callers) can detect and handle connection failures.
        if (php_sapi_name() === 'cli' || getenv('DB_TESTING') === '1') {
            throw $e;
        }

        http_response_code(500);
        echo "Database connection failed: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

if (!function_exists('tableExists')) {
    function tableExists(PDO $pdo, string $tableName): bool
    {
        try {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?'
            );
            $stmt->execute([$tableName]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>