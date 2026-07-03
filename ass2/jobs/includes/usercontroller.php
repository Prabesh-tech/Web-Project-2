<?php
/**
 * UserController - Handles user-related database operations
 * Separates business logic from views and entry points
 */

class UserController {
    /** @var PDO */
    private $pdo;

    /**
     * @param PDO $pdo
     */
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
            $sql = "SELECT id, username, email, role, createdAt FROM users ORDER BY createdAt DESC";
            
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
            $stmt = $this->pdo->prepare("SELECT id, username, email, role, createdAt FROM users WHERE id = ?");
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
            $stmt = $this->pdo->prepare("SELECT id, username, email, role, createdAt FROM users WHERE username = ?");
            $stmt->execute([trim($username)]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error fetching user: " . $e->getMessage());
        }
    }

    /**
     * Get applications for a user with optional status filtering
     * @param int $userId
     * @param string|null $status
     * @return array Application records with job data
     */
    public function getUserApplications($userId, $status = null) {
        try {
            $userId = intval($userId);
            $sql = "SELECT a.id, a.jobId, a.fullName, a.email, a.phone, a.cv, a.coverLetter, a.status, a.appliedAt, a.updatedAt, j.title AS jobTitle"
                 . " FROM applications a"
                 . " LEFT JOIN jobs j ON j.id = a.jobId"
                 . " WHERE a.userId = ?";
            $params = [$userId];

            if ($status !== null && trim($status) !== '') {
                $sql .= " AND a.status = ?";
                $params[] = trim($status);
            }

            $sql .= " ORDER BY a.appliedAt DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception("Error fetching user applications: " . $e->getMessage());
        }
    }

    /**
     * Search users by username or email
     * @param string $query Search term
     * @return array Array of matching user records
     */
    public function searchUsers($query, $limit = null, $offset = 0) {
        try {
            $searchTerm = '%' . trim($query) . '%';
            $sql = "SELECT id, username, email, role, createdAt FROM users 
                 WHERE username LIKE ? OR email LIKE ? 
                 ORDER BY username ASC";
            $params = [$searchTerm, $searchTerm];

            if ($limit !== null) {
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = intval($limit);
                $params[] = intval($offset);
            }

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } catch (PDOException $e) {
            throw new Exception("Error searching users: " . $e->getMessage());
        }
    }

    /**
     * Get total user count
     * @param string|null $search Optional search filter
     * @return int Total number of users
     */
    public function getUserCount($search = null) {
        try {
            if ($search !== null && trim($search) !== '') {
                $searchTerm = '%' . trim($search) . '%';
                $stmt = $this->pdo->prepare(
                    'SELECT COUNT(*) FROM users WHERE username LIKE ? OR email LIKE ?'
                );
                $stmt->execute([$searchTerm, $searchTerm]);
            } else {
                $stmt = $this->pdo->query('SELECT COUNT(*) FROM users');
            }
            return intval($stmt->fetchColumn());
        } catch (PDOException $e) {
            throw new Exception("Error counting users: " . $e->getMessage());
        }
    }

    /**
     * Get admin count
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
            $role = intval($isAdmin);

            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password, role, createdAt) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$username, $email, $hashedPassword, $role, date('Y-m-d H:i:s')]);

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
                $role = intval($isAdmin);
                $stmt = $this->pdo->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
                $stmt->execute([$email, $role, $userId]);
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
     * Get admin count
     * @return int Number of admin users
     */
    public function getAdminCount() {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role > 0");
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

    /**
     * Authenticate user with email and password
     * @param string $email
     * @param string $password
     * @return array|null User record if authenticated, null otherwise
     */
    public function authenticateWithEmail($email, $password) {
        try {
            $email = trim($email);
            $stmt = $this->pdo->prepare("SELECT id, username, email, password, role, createdAt FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Try bcrypt password verification first (for production)
                if (password_verify($password, $user['password'])) {
                    // Remove password from returned user data
                    unset($user['password']);
                    return $user;
                }
                
                // Fallback to plaintext comparison for testing (development only)
                if ($password === $user['password']) {
                    // Remove password from returned user data
                    unset($user['password']);
                    return $user;
                }
            }

            return null;
        } catch (Exception $e) {
            throw new Exception("Authentication error: " . $e->getMessage());
        }
    }
}
