<?php
/**
 * UserController - Handles user-related database operations
 * Separates business logic from views and entry points
 */

class UserController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all users with optional filtering
     * @param int $limit Optional pagination limit
     * @param int $offset Optional pagination offset
     * @return array Array of user records
     */
    public function getAllUsers($limit = null, $offset = 0) {
        try {
            $sql = "SELECT id, username, email, isAdmin, createdAt FROM users ORDER BY createdAt DESC";
            
            if ($limit !== null) {
                $sql .= " LIMIT ? OFFSET ?";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$limit, $offset]);
            } else {
                $stmt = $this->pdo->query($sql);
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            throw new Exception("Error fetching users: " . $e->getMessage());
        }
    }

    /**
     * Get user by ID
     * @param int $userId
     * @return array|null User record or null if not found
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, isAdmin, createdAt FROM users WHERE id = ?");
            $stmt->execute([intval($userId)]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }

    /**
     * Get user by username
     * @param string $username
     * @return array|null User record or null if not found
     */
    public function getUserByUsername($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, email, isAdmin, createdAt FROM users WHERE username = ?");
            $stmt->execute([trim($username)]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }

    /**
     * Search users by username or email
     * @param string $query Search term
     * @return array Array of matching user records
     */
    public function searchUsers($query) {
        try {
            $searchTerm = '%' . trim($query) . '%';
            $stmt = $this->pdo->prepare(
                "SELECT id, username, email, isAdmin, createdAt FROM users 
                 WHERE username LIKE ? OR email LIKE ? 
                 ORDER BY username ASC"
            );
            $stmt->execute([$searchTerm, $searchTerm]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            throw new Exception("Error searching users: " . $e->getMessage());
        }
    }

    /**
     * Create new user
     * @param string $username
     * @param string $email
     * @param string $password Plain password (will be hashed)
     * @param int $isAdmin 0=regular, 1=admin, 2=super admin
     * @return int New user ID
     */
    public function createUser($username, $email, $password, $isAdmin = 0) {
        try {
            $username = trim($username);
            $email = trim($email);
            
            if (empty($username) || strlen($username) < 3) {
                throw new Exception("Username must be at least 3 characters");
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address");
            }
            if (empty($password) || strlen($password) < 6) {
                throw new Exception("Password must be at least 6 characters");
            }

            // Check if username exists
            $existingUser = $this->getUserByUsername($username);
            if ($existingUser) {
                throw new Exception("Username already exists");
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $isAdmin = intval($isAdmin);

            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password, isAdmin, createdAt) 
                 VALUES (?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$username, $email, $hashedPassword, $isAdmin]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    /**
     * Update user
     * @param int $userId
     * @param string $email
     * @param int $isAdmin
     * @return bool Success status
     */
    public function updateUser($userId, $email, $isAdmin = null) {
        try {
            $userId = intval($userId);
            $email = trim($email);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email address");
            }

            if ($isAdmin !== null) {
                $isAdmin = intval($isAdmin);
                $stmt = $this->pdo->prepare("UPDATE users SET email = ?, isAdmin = ? WHERE id = ?");
                $stmt->execute([$email, $isAdmin, $userId]);
            } else {
                $stmt = $this->pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$email, $userId]);
            }

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error updating user: " . $e->getMessage());
        }
    }

    /**
     * Update user password
     * @param int $userId
     * @param string $newPassword
     * @return bool Success status
     */
    public function updatePassword($userId, $newPassword) {
        try {
            $userId = intval($userId);
            $newPassword = trim($newPassword);

            if (empty($newPassword) || strlen($newPassword) < 6) {
                throw new Exception("Password must be at least 6 characters");
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error updating password: " . $e->getMessage());
        }
    }

    /**
     * Delete user
     * @param int $userId
     * @return bool Success status
     */
    public function deleteUser($userId) {
        try {
            $userId = intval($userId);

            if ($userId <= 0) {
                throw new Exception("Invalid user ID");
            }

            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error deleting user: " . $e->getMessage());
        }
    }

    /**
     * Get total user count
     * @return int Total number of users
     */
    public function getUserCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            throw new Exception("Error counting users: " . $e->getMessage());
        }
    }

    /**
     * Get admin count
     * @return int Number of admin users
     */
    public function getAdminCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE isAdmin > 0");
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            throw new Exception("Error counting admins: " . $e->getMessage());
        }
    }

    /**
     * Authenticate user with username and password
     * @param string $username
     * @param string $password
     * @return array|null User record if authenticated, null otherwise
     */
    public function authenticate($username, $password) {
        try {
            $username = trim($username);
            $user = $this->getUserByUsername($username);

            if ($user && password_verify($password, $this->getUserPasswordHash($username))) {
                return $user;
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Authentication error: " . $e->getMessage());
        }
    }

    /**
     * Get password hash for user (internal use)
     * @param string $username
     * @return string|null Password hash or null if user not found
     */
    private function getUserPasswordHash($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT password FROM users WHERE username = ?");
            $stmt->execute([trim($username)]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['password'] ?? null;
        } catch (PDOException $e) {
            throw new Exception("Error fetching password: " . $e->getMessage());
        }
    }
}
