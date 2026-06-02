# Layout System Documentation

This project uses a **modular layout system** with 5 distinct layout types for different page contexts. Each layout has its own structure, styling, and use cases.

## Available Layouts

### 1. **Main Layout** (`layout-main.php`)
**Used for:** Home page, general public pages
**Features:**
- Full header with navigation
- Main content area
- Optional breadcrumbs
- Full footer
- No sidebar

**Example Usage:**
```php
<?php
$pageTitle = "Home - Prabesh Job";
$breadcrumbs = [
    'Home' => 'index.php',
];
$content = '<h1>Welcome</h1><p>Content here</p>';

include 'layouts/layout-main.php';
?>
```

**CSS File:** `assets/style.css` (base styles)

---

### 2. **Admin Layout** (`layout-admin.php`)
**Used for:** Admin dashboard, category management, admin controls
**Features:**
- Fixed left sidebar with navigation
- Admin-specific header with user info
- Automatic admin authentication check
- Active page highlighting
- Tables, cards, and action buttons

**Example Usage:**
```php
<?php
$pageTitle = "Manage Categories";
$breadcrumbs = [
    'Dashboard' => 'admin.php',
    'Categories' => 'adminCategories.php',
];
$content = '<!-- Your admin content here -->';

include 'layouts/layout-admin.php';
?>
```

**CSS File:** `assets/admin-style.css`

---

### 3. **Form Layout** (`layout-form.php`)
**Used for:** Login, register, add/edit forms, category forms
**Features:**
- Centered form container
- Focused, distraction-free design
- Error and success message displays
- Optional header
- Optional footer
- Responsive design

**Example Usage:**
```php
<?php
$pageTitle = "Add New Category";
$formTitle = "Add Category";
$formDescription = "Create a new job category";
$error = ($error !== '') ? $error : null;
$content = '
<form method="POST">
    <div class="form-group">
        <label for="name">Category Name</label>
        <input type="text" id="name" name="name" placeholder="e.g., IT - Programming">
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-submit">Add Category</button>
        <a href="adminCategories.php" class="btn-cancel">Cancel</a>
    </div>
</form>
';

include 'layouts/layout-form.php';
?>
```

**CSS File:** `assets/form-style.css`

---

### 4. **Detail Layout** (`layout-detail.php`)
**Used for:** Job detail pages, job application pages, individual item views
**Features:**
- Two-column layout (content + sidebar)
- Breadcrumbs
- Related items section
- Full header and footer
- Sidebar for metadata/actions

**Example Usage:**
```php
<?php
$pageTitle = "Senior Developer - XYZ Company";
$breadcrumbs = [
    'Home' => 'index.php',
    'Jobs' => 'search.php',
    'Senior Developer' => '#',
];
$content = '
<h1>Senior Developer</h1>
<p>Job description and details here...</p>
';
$sidebar = '
<div class="sidebar-card">
    <h3>Company</h3>
    <p>XYZ Company</p>
    <button class="sidebar-button">Apply Now</button>
</div>
';
$related = '<!-- Related jobs HTML -->';

include 'layouts/layout-detail.php';
?>
```

**CSS File:** `assets/detail-style.css`

---

### 5. **List Layout** (`layout-list.php`)
**Used for:** Job listings, search results, category listings
**Features:**
- Sidebar filters
- Search results header
- Pagination
- View/sort controls
- Grid or list view of items

**Example Usage:**
```php
<?php
$pageTitle = "Job Search";
$contentTitle = "Available Jobs";
$breadcrumbs = [
    'Home' => 'index.php',
    'Search' => 'search.php',
];
$filters = '
<div class="filter-group">
    <h3>Category</h3>
    <div class="filter-option">
        <input type="checkbox" id="cat1" name="category" value="1">
        <label for="cat1">IT & Programming</label>
    </div>
</div>
';
$controls = '<select class="sort-control"><option>Sort by</option></select>';
$content = '<!-- Job listing items -->';
$pagination = '<!-- Pagination links -->';

include 'layouts/layout-list.php';
?>
```

**CSS File:** `assets/list-style.css`

---

## Directory Structure

```
ass2/jobs/
├── layouts/
│   ├── layout-main.php          # Main layout template
│   ├── layout-admin.php         # Admin layout template
│   ├── layout-form.php          # Form layout template
│   ├── layout-detail.php        # Detail layout template
│   └── layout-list.php          # List layout template
├── assets/
│   ├── style.css                # Base styles (global)
│   ├── admin-style.css          # Admin layout styles
│   ├── form-style.css           # Form layout styles
│   ├── detail-style.css         # Detail layout styles
│   └── list-style.css           # List layout styles
├── includes/
│   ├── header.php               # Shared header component
│   ├── footer.php               # Shared footer component
│   └── ...other includes
└── ...pages
```

---

## Common Variables

Each layout accepts certain variables in the `$_SESSION` or local scope:

### Global Variables
- `$pageTitle` - Page title (used in `<title>` tag)
- `$breadcrumbs` - Array of breadcrumb labels and URLs

### Layout-Specific
- **Main:** `$content`
- **Admin:** `$content`, `$breadcrumbs`
- **Form:** `$formTitle`, `$formDescription`, `$error`, `$success`, `$content`, `$formFooter`
- **Detail:** `$content`, `$sidebar`, `$related`
- **List:** `$contentTitle`, `$filters`, `$controls`, `$content`, `$pagination`

---

## CSS Classes & Utilities

### Buttons
- `.btn-submit` - Primary button (blue)
- `.btn-cancel` - Secondary button (gray)
- `.btn-add` - Green action button
- `.btn-edit` - Edit button (blue)
- `.btn-delete` - Delete button (red)
- `.sidebar-button` - Sidebar action button

### Cards
- `.admin-card` - Dark card container
- `.sidebar-card` - Sidebar card (detail layout)
- `.list-item` - List item container
- `.related-item` - Related item card

### Tables
- `.admin-table` - Styled data table
- `thead` / `tbody` - Table structure

### Forms
- `.form-group` - Form field wrapper
- `.form-actions` - Form button container
- `.alert`, `.alert-error`, `.alert-success` - Message displays

---

## Responsive Design

All layouts are **fully responsive** and adapt to mobile devices:
- **Desktop:** Full layout with all features
- **Tablet:** Adjusted spacing and grid columns
- **Mobile:** Stacked layout, simplified navigation

---

## Color Scheme

- **Background:** `#0b0b0b` (dark)
- **Surface:** `#1a1a1a` (slightly lighter)
- **Primary:** `#2563eb` (blue)
- **Text:** `#ffffff` / `#d1d5db` / `#9ca3af`
- **Accent:** `#10b981` (green), `#ef4444` (red)

---

## Migration Guide

If you have existing pages that need to use these layouts:

1. **Extract the content** into a variable
2. **Set the layout variables** (title, breadcrumbs, etc.)
3. **Include the appropriate layout** file
4. **Remove old header/footer** calls

**Before:**
```php
<?php
include 'includes/header.php';
?>
<h1>My Page</h1>
<p>Content here</p>
<?php
include 'includes/footer.php';
?>
```

**After:**
```php
<?php
$pageTitle = "My Page";
$content = '<h1>My Page</h1><p>Content here</p>';

include 'layouts/layout-main.php';
?>
```

---

## Tips

✅ **Do:**
- Use the appropriate layout for each page type
- Keep content in the `$content` variable before including layout
- Use breadcrumbs for better navigation
- Leverage the existing CSS classes

❌ **Don't:**
- Modify layout files directly (instead, extend via CSS)
- Mix layout types in a single page
- Override global styles for individual pages
- Create duplicate header/footer code

---

## Support

For questions or issues with layouts:
1. Check the layout file comments
2. Review the CSS files for available classes
3. Look at similar pages for examples
