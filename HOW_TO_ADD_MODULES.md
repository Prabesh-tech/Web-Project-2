# How to Add New Modules to Prabesh Job Site

## Quick Reference

This guide shows how to create a new module following the established MVC pattern.

---

## Step 1: Create the Controller

Create `includes/[module]controller.php` with a class containing database functions.

```php
<?php
/**
 * YourModuleController - Handles your-module database operations
 */

class YourModuleController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Public functions that handle business logic
    public function getAllItems($limit = null, $offset = 0) {
        try {
            $sql = "SELECT * FROM your_table ORDER BY id DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT ? OFFSET ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$limit, $offset]);
            } else {
                $stmt = $this->pdo->query($sql);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            throw new Exception("Error fetching items: " . $e->getMessage());
        }
    }

    public function getItemById($itemId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM your_table WHERE id = ?");
            $stmt->execute([intval($itemId)]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching item: " . $e->getMessage());
        }
    }

    public function createItem($param1, $param2) {
        try {
            // Validate inputs
            if (empty($param1)) {
                throw new Exception("Param1 is required");
            }

            // Execute query
            $stmt = $this->pdo->prepare(
                "INSERT INTO your_table (field1, field2) VALUES (?, ?)"
            );
            $stmt->execute([$param1, $param2]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error creating item: " . $e->getMessage());
        }
    }

    public function updateItem($itemId, $param1, $param2) {
        try {
            $itemId = intval($itemId);
            
            $stmt = $this->pdo->prepare(
                "UPDATE your_table SET field1 = ?, field2 = ? WHERE id = ?"
            );
            $stmt->execute([$param1, $param2, $itemId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error updating item: " . $e->getMessage());
        }
    }

    public function deleteItem($itemId) {
        try {
            $itemId = intval($itemId);

            if ($itemId <= 0) {
                throw new Exception("Invalid item ID");
            }

            $stmt = $this->pdo->prepare("DELETE FROM your_table WHERE id = ?");
            $stmt->execute([$itemId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error deleting item: " . $e->getMessage());
        }
    }

    public function getItemCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM your_table");
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            throw new Exception("Error counting items: " . $e->getMessage());
        }
    }
}
```

---

## Step 2: Create View Files

Create `.html.php` files in your module directory containing ONLY HTML display code.

### View File Template: `items/item.html.php`

```html
<?php
/**
 * Item Detail View
 * Displays single item information
 */
?>

<div class="item-detail">
    <h1><?= htmlspecialchars($item['title']) ?></h1>
    
    <div class="item-info">
        <p><strong>Description:</strong></p>
        <p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
        
        <p><strong>Created:</strong> <?= date('M d, Y', strtotime($item['createdAt'])) ?></p>
    </div>

    <a href="edit.php?id=<?= intval($item['id']) ?>" class="btn btn-primary">Edit</a>
    <a href="list.php" class="btn btn-secondary">Back to List</a>
</div>
```

### View File Template: `items/addItem.html.php`

```html
<?php
/**
 * Add Item Form View
 */
?>

<form method="POST" class="item-form">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" required 
            value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
            class="form-control">
    </div>

    <div class="form-group">
        <label for="description">Description *</label>
        <textarea id="description" name="description" required
            class="form-control"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Create Item</button>
    <a href="list.php" class="btn btn-secondary">Cancel</a>
</form>
```

### View File Template: `items/listItems.html.php`

```html
<?php
/**
 * Items List View
 */
?>

<div class="items-list">
    <div class="list-header">
        <h1>Items</h1>
        <a href="add.php" class="btn btn-primary">Add New Item</a>
    </div>

    <?php if (!empty($items)): ?>
        <table class="items-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= intval($item['id']) ?></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= date('M d, Y', strtotime($item['createdAt'])) ?></td>
                        <td>
                            <a href="item.php?id=<?= intval($item['id']) ?>">View</a>
                            <a href="edit.php?id=<?= intval($item['id']) ?>">Edit</a>
                            <a href="delete.php?id=<?= intval($item['id']) ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No items found</p>
    <?php endif; ?>
</div>
```

---

## Step 3: Create Entry Point Files

Create `.php` files that use the controller and views.

### Entry Point Template: `items/item.php`

```php
<?php
/**
 * Item Detail Page - Controller
 * Displays single item
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/yourmodulecontroller.php';

try {
    $itemController = new YourModuleController($pdo);

    $itemId = intval($_GET['id'] ?? 0);
    if ($itemId <= 0) {
        throw new Exception("Invalid item ID");
    }

    $item = $itemController->getItemById($itemId);
    if (!$item) {
        throw new Exception("Item not found");
    }

    $pageTitle = htmlspecialchars($item['title']);
    $breadcrumbs = [
        'Home' => 'index.php',
        'Items' => 'items/list.php',
        'Detail' => '#',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once __DIR__ . '/item.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
```

### Entry Point Template: `items/add.php`

```php
<?php
/**
 * Add Item Page - Controller
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/yourmodulecontroller.php';

// Check authentication (optional)
if (empty($_SESSION['user']) || intval($_SESSION['user']['isAdmin'] ?? 0) < 1) {
    header('Location: ../login.php');
    exit;
}

try {
    $error = '';
    $success = false;

    $itemController = new YourModuleController($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        try {
            $itemController->createItem($title, $description);
            header('Location: list.php?status=added');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    $pageTitle = 'Add Item';
    ob_start();
    require_once 'addItem.html.php';
    $content = ob_get_clean();

    require_once __DIR__ . '/../layouts/layout-form.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
```

### Entry Point Template: `items/list.php`

```php
<?php
/**
 * Items List Page - Controller
 */

session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/yourmodulecontroller.php';

try {
    $error = '';
    $success = false;

    $itemController = new YourModuleController($pdo);

    // Pagination
    $page = intval($_GET['page'] ?? 1);
    $perPage = 10;
    $offset = ($page - 1) * $perPage;

    $itemCount = $itemController->getItemCount();
    $totalPages = ceil($itemCount / $perPage);

    $items = $itemController->getAllItems($perPage, $offset);

    if (isset($_GET['status']) && $_GET['status'] === 'added') {
        $success = true;
    }

    $pageTitle = 'Items';
    $breadcrumbs = [
        'Home' => 'index.php',
        'Items' => '#',
    ];

    require_once __DIR__ . '/../includes/header.php';
    require_once 'listItems.html.php';
    require_once __DIR__ . '/../includes/footer.php';

} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
```

---

## Step 4: Directory Structure

Create the following directory structure:

```
ass2/jobs/
├── includes/
│   ├── yourmodulecontroller.php      ← Controller
│
└── items/                             ← Module directory
    ├── item.php                       ← Entry point
    ├── item.html.php                  ← View
    ├── add.php                        ← Entry point
    ├── addItem.html.php               ← View
    ├── edit.php                       ← Entry point
    ├── editItem.html.php              ← View
    ├── list.php                       ← Entry point
    └── listItems.html.php             ← View
```

---

## Key Patterns

### Pattern 1: Input Validation in Controller
```php
public function createItem($title, $description) {
    $title = trim($title);
    $description = trim($description);
    
    if (empty($title) || strlen($title) < 3) {
        throw new Exception("Title must be at least 3 characters");
    }
    
    // ... rest of function
}
```

### Pattern 2: Error Handling in Entry Point
```php
try {
    $item = $itemController->getItemById($itemId);
    if (!$item) {
        throw new Exception("Item not found");
    }
    // ... rest of code
} catch (Exception $e) {
    die("<h2>Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p>");
}
```

### Pattern 3: Form Pre-population in View
```html
<input type="text" name="title" 
    value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
```

### Pattern 4: Output Escaping in View
```html
<h1><?= htmlspecialchars($item['title']) ?></h1>
<p><?= nl2br(htmlspecialchars($item['description'])) ?></p>
```

---

## Common Gotchas

1. **Always escape output** - Use htmlspecialchars() for user input
2. **Always validate input** - Check in controller before database
3. **Always use prepared statements** - Never concatenate SQL
4. **Always handle exceptions** - Try-catch in entry points
5. **Never put queries in views** - Controllers only
6. **Never put HTML in controllers** - Views only

---

## Testing Your Module

1. Create a test item
2. View the detail page
3. Edit the item
4. Delete the item
5. Verify success/error messages
6. Check pagination works

---

## Extending the Module

To add new functionality:

1. **Add function to controller** - Write the business logic
2. **Create new view file** - Add HTML display
3. **Create entry point** - Wire up controller to view
4. **Test thoroughly** - Verify all operations

Example: Add search functionality
```php
// In controller
public function searchItems($query) {
    $searchTerm = '%' . trim($query) . '%';
    $stmt = $this->pdo->prepare(
        "SELECT * FROM your_table WHERE title LIKE ? OR description LIKE ?"
    );
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
}

// In entry point
if (!empty($_GET['q'])) {
    $items = $itemController->searchItems($_GET['q']);
} else {
    $items = $itemController->getAllItems();
}

// In view
<form method="GET">
    <input type="text" name="q" placeholder="Search...">
    <button>Search</button>
</form>
```

---

That's it! Follow these patterns and you can quickly add new modules to the Prabesh Job Site.
