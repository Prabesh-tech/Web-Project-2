# MVC Architecture Guide - Category Module Refactoring

## Overview

This guide explains the **Model-View-Controller (MVC)** pattern used in the job site, using the **Category module** as the reference implementation.

---

## Directory Structure

```
ass2/jobs/
├── includes/
│   ├── categorycontroller.php     ← CONTROLLER (Business Logic)
│   ├── DbConnection.php           ← DATABASE CONNECTION
│   ├── header.php                 ← SHARED COMPONENT
│   └── footer.php                 ← SHARED COMPONENT
│
├── category.html.php              ← VIEW (Display Only)
├── addCategory.html.php           ← VIEW (Display Only)
├── editCategory.html.php          ← VIEW (Display Only)
├── adminCategories.html.php       ← VIEW (Display Only)
│
├── category.php                   ← ROUTE/CONTROLLER (Entry Point)
├── addCategory.php                ← ROUTE/CONTROLLER (Entry Point)
├── editCategory.php               ← ROUTE/CONTROLLER (Entry Point)
├── adminCategories.php            ← ROUTE/CONTROLLER (Entry Point)
│
└── layouts/
    ├── layout-main.php
    ├── layout-form.php
    ├── layout-admin.php
    └── ...
```

---

## Pattern Explanation

### 1. **CONTROLLER** (includes/categorycontroller.php)
**Purpose:** Contains all business logic for category operations

**Features:**
- Database queries wrapped in functions
- Error handling with exceptions
- No HTML/display code
- Reusable functions

**Example Functions:**
```php
class CategoryController {
    // Get all categories
    public function getAllCategories()
    
    // Get single category
    public function getCategoryById($id)
    
    // Create new category
    public function createCategory($name, $description, $image)
    
    // Update category
    public function updateCategory($id, $name, $description, $image)
    
    // Delete category
    public function deleteCategory($id)
    
    // Search categories
    public function searchCategories($query)
}
```

---

### 2. **VIEW** (.html.php files)
**Purpose:** Display-only files with HTML markup

**Features:**
- No PHP logic (only conditionals for displaying)
- Variables passed from controller
- HTML/CSS only
- Clean and readable

**Example (addCategory.html.php):**
```php
<form method="POST" class="category-form">
    <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>">
    <button type="submit">Submit</button>
</form>
```

---

### 3. **ROUTE/ENTRY POINT** (.php files)
**Purpose:** Connects controller and view, handles user requests

**Features:**
- Includes controller
- Calls controller functions
- Catches errors
- Passes data to view
- Includes header/footer/layout

**Example Flow (addCategory.php):**
```
Request → Validation → Controller → View → Response
```

---

## Step-by-Step: How to Create a New Module

### Example: Creating a "Jobs" module following the same pattern

#### Step 1: Create the Controller
**File:** `includes/jobcontroller.php`

```php
<?php
class JobController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAllJobs() {
        $stmt = $this->pdo->query('SELECT * FROM jobs ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getJobById($id) {
        $stmt = $this->pdo->prepare('SELECT * FROM jobs WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function createJob($title, $description, $categoryId) {
        if (empty($title)) {
            throw new Exception("Job title is required");
        }
        
        $stmt = $this->pdo->prepare('INSERT INTO jobs (title, description, categoryId) VALUES (?, ?, ?)');
        $stmt->execute([$title, $description, $categoryId]);
        return $this->pdo->lastInsertId();
    }
    
    public function updateJob($id, $title, $description, $categoryId) {
        $stmt = $this->pdo->prepare('UPDATE jobs SET title=?, description=?, categoryId=? WHERE id=?');
        $stmt->execute([$title, $description, $categoryId, $id]);
        return true;
    }
    
    public function deleteJob($id) {
        $stmt = $this->pdo->prepare('DELETE FROM jobs WHERE id=?');
        $stmt->execute([$id]);
        return true;
    }
}
?>
```

---

#### Step 2: Create View Files

**File:** `addJob.html.php`
```php
<div class="form-container">
    <h1>Add New Job</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Job Title</label>
            <input type="text" name="title" required>
        </div>
        
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"></textarea>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-submit">Add Job</button>
            <a href="jobs/manageJobs.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
```

**File:** `listJobs.html.php`
```php
<div class="list-container">
    <h1>All Jobs</h1>
    
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?= htmlspecialchars($job['id']) ?></td>
                    <td><?= htmlspecialchars($job['title']) ?></td>
                    <td><?= htmlspecialchars($job['categoryId']) ?></td>
                    <td>
                        <a href="editJob.php?id=<?= $job['id'] ?>" class="btn-edit">Edit</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $job['id'] ?>">
                            <button type="submit" class="btn-delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
```

---

#### Step 3: Create Entry Point (Route)

**File:** `jobs/addJob.php`
```php
<?php
session_start();
require_once __DIR__ . '/../includes/DbConnection.php';
require_once __DIR__ . '/../includes/jobcontroller.php';

// Check admin access
if (!isset($_SESSION['user']['id'])) {
    header('Location: ../login.php');
    exit;
}

try {
    $error = '';
    $jobController = new JobController($pdo);
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = intval($_POST['categoryId'] ?? 0);
        
        try {
            $jobController->createJob($title, $description, $categoryId);
            header('Location: manageJobs.php?status=added');
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    // Set variables for view
    $pageTitle = 'Add New Job';
    $formTitle = 'Add New Job';
    
    // Include header
    require_once __DIR__ . '/../includes/header.php';
    
    // Include view
    require_once 'addJob.html.php';
    
    // Include footer
    require_once __DIR__ . '/../includes/footer.php';
    
} catch (Exception $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>
```

---

## Benefits of This Structure

| Aspect | Benefit |
|--------|---------|
| **Separation of Concerns** | Logic, display, and routing are separate |
| **Reusability** | Controller functions can be used in multiple views |
| **Testing** | Easier to unit test controller logic |
| **Maintenance** | Easy to modify display without touching logic |
| **Scaling** | Easy to add new features to existing modules |
| **Debugging** | Clear flow makes issues easier to trace |

---

## File Naming Conventions

```
categorycontroller.php          ← Controller (lowercase "controller")
category.html.php              ← View (ends with ".html.php")
addCategory.html.php           ← Specific view (camelCase)
adminCategories.html.php       ← Admin view (camelCase)
category.php                   ← Entry point / Route (camelCase)
addCategory.php                ← Entry point / Route (camelCase)
editCategory.php               ← Entry point / Route (camelCase)
adminCategories.php            ← Entry point / Route (camelCase)
```

---

## Implementation Checklist

When creating a new module, follow this checklist:

- [ ] **1. Create Controller** (`includes/modulenamecontroller.php`)
  - [ ] Create class with constructor taking $pdo
  - [ ] Add getAll(), getById(), create(), update(), delete() methods
  - [ ] Add error handling with exceptions

- [ ] **2. Create Views** (`.html.php` files)
  - [ ] Create view files for each page type
  - [ ] Use variables passed from controller
  - [ ] No logic, only display

- [ ] **3. Create Entry Points** (`.php` files)
  - [ ] Create route file for each action
  - [ ] Include controller
  - [ ] Handle form submissions
  - [ ] Pass data to view
  - [ ] Include header/footer/layout

- [ ] **4. Test**
  - [ ] Test create functionality
  - [ ] Test read functionality
  - [ ] Test update functionality
  - [ ] Test delete functionality
  - [ ] Test error handling

---

## Common Patterns

### Pattern 1: List with Actions
```php
// Controller: getAllItems()
// View: listItems.html.php
// Route: manageItems.php
```

### Pattern 2: Detail View
```php
// Controller: getItemById($id)
// View: item.html.php
// Route: item.php?id=123
```

### Pattern 3: Add Form
```php
// Controller: createItem($data)
// View: addItem.html.php
// Route: addItem.php (POST)
```

### Pattern 4: Edit Form
```php
// Controller: updateItem($id, $data)
// View: editItem.html.php
// Route: editItem.php?id=123 (POST)
```

### Pattern 5: Delete
```php
// Controller: deleteItem($id)
// Route: deleteItem.php (POST)
// Redirect to list
```

---

## Error Handling

All controllers use exceptions:

```php
try {
    $controller->createItem($data);
} catch (Exception $e) {
    $error = $e->getMessage();  // Display to user
}
```

Views display errors:
```php
<?php if (!empty($error)): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>
```

---

## Summary

This MVC pattern provides:
1. **Clean code** - Logic separated from display
2. **Easy maintenance** - Know exactly where to look
3. **Consistency** - Same pattern across all modules
4. **Scalability** - Easy to add new modules
5. **Testability** - Controllers can be tested independently

Use this as your template for all new features!
