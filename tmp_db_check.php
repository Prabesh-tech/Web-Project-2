<?php
try {
    $pdo = new PDO("mysql:host=mysql;dbname=Assignments;charset=utf8mb4", "user", "password", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $users = $pdo->query("SELECT id, username, email, isAdmin FROM users LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $cats = $pdo->query("SELECT id, name FROM categories LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    echo "USERS:\n";
    foreach ($users as $r) {
        echo $r['id'] . "\t" . $r['username'] . "\t" . $r['email'] . "\t" . $r['isAdmin'] . "\n";
    }
    echo "CATS:\n";
    foreach ($cats as $r) {
        echo $r['id'] . "\t" . $r['name'] . "\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
