<?php
// CLI test script to verify createJob works with a null companyId and jobseeker posting
putenv('DB_DRIVER=sqlite');
putenv('DB_NAME=:memory:');
require __DIR__ . '/../includes/DbConnection.php';
require __DIR__ . '/../includes/jobcontroller.php';

// Create minimal schema
$pdo->exec('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL)');
$pdo->exec('CREATE TABLE companies (id INTEGER PRIMARY KEY AUTOINCREMENT, companyName TEXT NOT NULL, logo TEXT)');
$pdo->exec('CREATE TABLE jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    salary TEXT,
    categoryId INTEGER,
    companyId INTEGER,
    location TEXT,
    jobType TEXT,
    closingDate TEXT,
    createdAt TEXT
)');

$pdo->exec("INSERT INTO categories (name) VALUES ('Software Development')");
$pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('ABC Tech', 'abc-logo.png')");

// Simulate a logged-in jobseeker
$sessionUser = ['id' => 42, 'role' => 0, 'username' => 'jobseeker'];

$jobController = new JobController($pdo);
try {
    $jobId = $jobController->createJob('Test Title', 'Test description', 1, '50000', null, 'Kathmandu', $sessionUser['id'], 'Full-time', '2026-12-31');
    echo "Created job id: $jobId\n";
    $stmt = $pdo->prepare('SELECT * FROM jobs WHERE id = ?');
    $stmt->execute([$jobId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($row);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
