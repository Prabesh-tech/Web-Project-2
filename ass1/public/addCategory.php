<?php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';

if (empty($_SESSION['user']) || !in_array(intval($_SESSION['user']['isAdmin'] ?? 0), [1,2], true)) {
    header('Location: login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $check = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $check->execute([$name]);
        if ($check->rowCount() > 0) {
            $error = "Category already exists. Please use the existing category.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$name]);
            header('Location: adminCategories.php');
            exit;
        }
    } else {
        $error = "Category name required.";
    }
} 
?>
<!DOCTYPE html>
<html>
<head><title>Add Category</title></head>
<body>
<h1>Add Category</h1>
<?php if ($error): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
<form method="POST">
    <input type="text" name="name" placeholder="Category name">
    <button type="submit">Add</button>
</form>
</body>
</html>
