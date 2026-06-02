# MVC Quick Reference Guide

## What is MVC?

**MVC = Model-View-Controller**

```
User Request
    ↓
 Controller (Process)
    ↓
Database ← → Model (Data)
    ↓
    View (Display)
    ↓
HTML Response
```

---

## Three Layers

### 1️⃣ **CONTROLLER** (Business Logic)
**File:** `includes/categorycontroller.php`

- Contains class with functions
- Handles database operations
- Input validation
- Error handling
- No HTML

```php
class CategoryController {
    public function createCategory($name, $description) { ... }
    public function getCategory($id) { ... }
    public function updateCategory($id, $name) { ... }
    public function deleteCategory($id) { ... }
}
```

### 2️⃣ **VIEW** (Display)
**Files:** `*.html.php`

- Only HTML and display
- Show data from controller
- No business logic
- No database queries
- Variables only (e.g., $category, $error)

```php
<h1><?= htmlspecialchars($category['name']) ?></h1>
<p><?= htmlspecialchars($category['description']) ?></p>
```

### 3️⃣ **ROUTE** (Entry Point)
**Files:** `*.php`

- Receives user request
- Calls controller functions
- Passes data to view
- Handles errors
- Includes header/footer

```php
<?php
$controller = new CategoryController($pdo);
$category = $controller->getCategory($_GET['id']);
include 'category.html.php';
?>
```

---

## File Naming Pattern

| Purpose | Pattern | Example |
|---------|---------|---------|
| Controller Class | `modulenamecontroller.php` | `categorycontroller.php` |
| List View | `list[Module].html.php` | `listCategories.html.php` |
| Detail View | `[module].html.php` | `category.html.php` |
| Add View | `add[Module].html.php` | `addCategory.html.php` |
| Edit View | `edit[Module].html.php` | `editCategory.html.php` |
| List Route | `manage[Modules].php` | `manageCategories.php` |
| Detail Route | `[module].php` | `category.php` |
| Add Route | `add[Module].php` | `addCategory.php` |
| Edit Route | `edit[Module].php` | `editCategory.php` |

---

## Controller Template

```php
<?php
// includes/modulecontroller.php
class ModuleController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Get all records
    public function getAll() {
        try {
            $stmt = $this->pdo->query('SELECT * FROM tablename');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching records: " . $e->getMessage());
        }
    }
    
    // Get single record
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM tablename WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching record: " . $e->getMessage());
        }
    }
    
    // Create record
    public function create($data) {
        // Validate
        if (empty($data['name'])) {
            throw new Exception("Name is required");
        }
        
        try {
            // Insert
            $stmt = $this->pdo->prepare('INSERT INTO tablename (name) VALUES (?)');
            $stmt->execute([$data['name']]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error creating record: " . $e->getMessage());
        }
    }
    
    // Update record
    public function update($id, $data) {
        try {
            $stmt = $this->pdo->prepare('UPDATE tablename SET name = ? WHERE id = ?');
            $stmt->execute([$data['name'], $id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error updating record: " . $e->getMessage());
        }
    }
    
    // Delete record
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM tablename WHERE id = ?');
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            throw new Exception("Error deleting record: " . $e->getMessage());
        }
    }
}
?>
```

---

## Route Template

```php
<?php
// add.php
session_start();
require_once __DIR__ . '/includes/DbConnection.php';
require_once __DIR__ . '/includes/modulecontroller.php';

// Check permissions
if (!isset($_SESSION['user']['id'])) {
    header('Location: login.php');
    exit;
}

try {
    $error = '';
    $controller = new ModuleController($pdo);
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $controller->create($_POST);
            header('Location: list.php?status=added');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    // Set page variables
    $pageTitle = 'Add New Item';
    
    // Include header
    require_once 'includes/header.php';
    
    // Include view
    require_once 'add.html.php';
    
    // Include footer
    require_once 'includes/footer.php';
    
} catch (Exception $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>
```

---

## View Template

```php
<?php
// add.html.php
?>

<div class="form-container">
    <h1><?= htmlspecialchars($pageTitle) ?></h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="form">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        
        <button type="submit" class="btn-submit">Submit</button>
    </form>
</div>
```

---

## Common Functions Checklist

Every controller should have these basic functions:

- ✅ `getAll()` - Get all records
- ✅ `getById($id)` - Get single record
- ✅ `create($data)` - Create new record
- ✅ `update($id, $data)` - Update record
- ✅ `delete($id)` - Delete record
- ✅ `search($query)` - Search records
- ✅ `count()` - Get total count
- ✅ `validate($data)` - Validate input
- ✅ Exception handling for all methods

---

## Error Handling Pattern

```php
// In Controller
try {
    $result = $controller->create($data);
    // Success
} catch (Exception $e) {
    $error = $e->getMessage();
}

// In View
<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
```

---

## Data Flow Example

### User adds a category:

```
1. User fills form and clicks "Add"
   ↓
2. Form POSTs to addCategory.php
   ↓
3. addCategory.php instantiates CategoryController
   ↓
4. Calls $controller->createCategory($name, $desc, $image)
   ↓
5. Controller validates input
   ↓
6. Controller checks for duplicates
   ↓
7. Controller inserts into database
   ↓
8. Controller returns ID (success)
   ↓
9. addCategory.php redirects to adminCategories.php
   ↓
10. User sees success message with new category
```

---

## Best Practices

✅ **DO:**
- Put all database logic in controller
- Keep views display-only
- Use try-catch for error handling
- Validate input in controller
- Use htmlspecialchars() in views
- Create specific functions for each operation
- Document function parameters and returns
- Check user permissions in routes

❌ **DON'T:**
- Mix HTML and PHP logic
- Query database in views
- Put HTML in controller
- Skip error handling
- Trust user input without validation
- Create massive functions that do everything
- Hardcode values in views
- Forget to sanitize output

---

## Testing MVC Code

### Test Controller:
```php
$controller = new CategoryController($pdo);

// Test create
try {
    $id = $controller->createCategory('Test', 'Description');
    echo "✅ Create works: ID = $id";
} catch (Exception $e) {
    echo "❌ Create failed: " . $e->getMessage();
}

// Test getById
$cat = $controller->getCategoryById($id);
echo "✅ Category: " . $cat['name'];

// Test update
$controller->updateCategory($id, 'Updated', 'New desc');
echo "✅ Update works";

// Test delete
$controller->deleteCategory($id);
echo "✅ Delete works";
```

---

## Summary

| Layer | Responsibility | Location |
|-------|-----------------|----------|
| Controller | Logic & Data | includes/modulecontroller.php |
| View | Display | module.html.php |
| Route | Request & Response | module.php |

This separation makes code:
- ✨ Cleaner
- 🔍 Easier to debug
- 📚 Easier to understand
- 🧪 Easier to test
- 🔄 Easier to reuse
- 📈 Easier to scale

---

## Quick Links

📖 **Documentation:**
- MVC_ARCHITECTURE.md - Full MVC guide
- CATEGORY_MODULE_STRUCTURE.md - Category example
- LAYOUT_DOCUMENTATION.md - Layout system guide

📁 **Example Module:**
- includes/categorycontroller.php - Controller example
- category.html.php - View example
- category.php - Route example

---

**Start building with MVC today! 🚀**
