<?php
require_once __DIR__ . '/includes/DbConnection.php';
require 'includes/header.php';

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['isAdmin'] != 1) {
    header("Location: login.php");
    exit;
}

/* DELETE USER */
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    if ($id == $_SESSION['user']['id']) {
        die("You cannot delete yourself");
    }

    $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: manageAdmins.php");
    exit;
}

/* TOGGLE ADMIN */
if (isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];

    $stmt = $pdo->prepare("SELECT isAdmin FROM user WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $newRole = $user['isAdmin'] ? 0 : 1;

        $update = $pdo->prepare("UPDATE user SET isAdmin = ? WHERE id = ?");
        $update->execute([$newRole, $id]);
    }

    header("Location: manageAdmins.php");
    exit;
}

/* FETCH USERS */
$stmt = $pdo->query("SELECT id, name, email, isAdmin FROM user");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-page">

    <h1 class="admin-title">Admin Dashboard</h1>

    <a href="index.php" class="btn-back">← Back to Home</a>

    <div class="table-wrapper">
        <table class="data-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <?= $user['isAdmin'] ? '<span class="badge admin">Yes</span>' : '<span class="badge user">No</span>' ?>
                        </td>
                        <td class="actions">

                            <a class="btn-small" href="manageAdmins.php?toggle=<?= $user['id'] ?>">
                                Toggle
                            </a>

                            <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                                <a class="btn-danger"
                                   onclick="return confirm('Delete this user?')"
                                   href="manageAdmins.php?delete=<?= $user['id'] ?>">
                                   Delete
                                </a>
                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

<?php require 'includes/footer.php'; ?>