<?php
/**
 * CategoryController
 * Handles all category-related operations
 */

require_once __DIR__ . '/DbConnection.php';

class CategoryController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        try {
            $stmt = $this->pdo->query('SELECT id, name, description, image FROM categories ORDER BY name ASC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching categories: " . $e->getMessage());
        }
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id) {
        try {
            $stmt = $this->pdo->prepare('SELECT id, name, description, image FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching category: " . $e->getMessage());
        }
    }

    /**
     * Get jobs in a category
     */
    public function getJobsByCategory($categoryId) {
        try {
            $stmt = $this->pdo->prepare('SELECT id, title, description, salary FROM jobs WHERE categoryId = ? ORDER BY id DESC');
            $stmt->execute([$categoryId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching jobs: " . $e->getMessage());
        }
    }

    /**
     * Create new category
     */
    public function createCategory($name, $description = '', $image = '') {
        $name = trim($name);
        $description = trim($description);
        $image = trim($image);

        if (empty($name)) {
            throw new Exception("Category name is required");
        }

        try {
            // Check if already exists
            $check = $this->pdo->prepare('SELECT id FROM categories WHERE name = ?');
            $check->execute([$name]);
            
            if ($check->rowCount() > 0) {
                throw new Exception("This category already exists");
            }

            // Insert new category
            $stmt = $this->pdo->prepare('INSERT INTO categories (name, description, image) VALUES (?, ?, ?)');
            $stmt->execute([$name, $description, $image]);
            
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Update category
     */
    public function updateCategory($id, $name, $description = '', $image = '') {
        $id = intval($id);
        $name = trim($name);
        $description = trim($description);
        $image = trim($image);

        if ($id <= 0) {
            throw new Exception("Invalid category ID");
        }

        if (empty($name)) {
            throw new Exception("Category name is required");
        }

        try {
            // Check if category exists
            $check = $this->pdo->prepare('SELECT id FROM categories WHERE id = ?');
            $check->execute([$id]);
            
            if ($check->rowCount() === 0) {
                throw new Exception("Category not found");
            }

            // Check for duplicate names (excluding current category)
            $checkDup = $this->pdo->prepare('SELECT id FROM categories WHERE name = ? AND id != ?');
            $checkDup->execute([$name, $id]);
            
            if ($checkDup->rowCount() > 0) {
                throw new Exception("Category name already exists");
            }

            // Update category
            $updateFields = ['name' => $name, 'description' => $description];
            if (!empty($image)) {
                $updateFields['image'] = $image;
            }

            $setClause = implode(', ', array_map(fn($key) => "$key = ?", array_keys($updateFields)));
            $values = array_values($updateFields);
            $values[] = $id;

            $stmt = $this->pdo->prepare("UPDATE categories SET $setClause WHERE id = ?");
            $stmt->execute($values);
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Delete category
     */
    public function deleteCategory($id) {
        $id = intval($id);

        if ($id <= 0) {
            throw new Exception("Invalid category ID");
        }

        try {
            // Check if category exists
            $check = $this->pdo->prepare('SELECT id FROM categories WHERE id = ?');
            $check->execute([$id]);
            
            if ($check->rowCount() === 0) {
                throw new Exception("Category not found");
            }

            // Check if category has jobs
            $jobs = $this->pdo->prepare('SELECT COUNT(*) as count FROM jobs WHERE categoryId = ?');
            $jobs->execute([$id]);
            $result = $jobs->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                throw new Exception("Cannot delete category with existing jobs. Please delete jobs first.");
            }

            // Delete category
            $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            
            return true;
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Search categories by name
     */
    public function searchCategories($query) {
        $query = '%' . trim($query) . '%';
        try {
            $stmt = $this->pdo->prepare('SELECT id, name, description, image FROM categories WHERE name LIKE ? OR description LIKE ? ORDER BY name ASC');
            $stmt->execute([$query, $query]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error searching categories: " . $e->getMessage());
        }
    }

    /**
     * Get category image path
     */
    public function getCategoryImagePath($category) {
        if (empty($category['image'])) {
            return 'assets/images/image1.jpg';
        }

        $imageFile = $category['image'];

        // Check various possible locations
        if (strpos($imageFile, 'assets/') === 0) {
            return $imageFile;
        } elseif (file_exists(__DIR__ . '/../assets/images/' . $imageFile)) {
            return 'assets/images/' . htmlspecialchars($imageFile);
        } elseif (file_exists(__DIR__ . '/../images/auctions/' . $imageFile)) {
            return 'images/auctions/' . htmlspecialchars($imageFile);
        }

        return 'assets/images/image1.jpg';
    }

    /**
     * Get category count
     */
    public function getCategoryCount() {
        try {
            $stmt = $this->pdo->query('SELECT COUNT(*) as count FROM categories');
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            throw new Exception("Error counting categories: " . $e->getMessage());
        }
    }
}
?>
