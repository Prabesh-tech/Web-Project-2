<?php

class JobController
{
    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function hasColumn(string $table, string $column): bool
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        if ($driver === 'sqlite') {
            $stmt = $this->pdo->query("PRAGMA table_info($table)");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $row) {
                if (isset($row['name']) && $row['name'] === $column) {
                    return true;
                }
            }
            return false;
        }

        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?'
        );
        $stmt->execute([$table, $column]);
        return intval($stmt->fetchColumn()) > 0;
    }

    public function getAllJobs(?int $categoryId = null, ?int $limit = null, int $offset = 0, bool $includeArchived = false, ?string $search = null, ?string $createdAfter = null, ?string $createdBefore = null): array
    {
        $sql = "SELECT j.*, c.name AS categoryName, co.companyName, co.logo AS companyLogo
                FROM jobs j
                LEFT JOIN categories c ON c.id = j.categoryId
                LEFT JOIN companies co ON co.id = j.companyId";

        $params = [];
        $clauses = [];

        if ($categoryId !== null) {
            $clauses[] = 'j.categoryId = ?';
            $params[] = intval($categoryId);
        }

        if (!$includeArchived && $this->hasColumn('jobs', 'isArchived')) {
            $clauses[] = 'j.isArchived = 0';
        }

        if ($search !== null && $search !== '') {
            $clauses[] = '(j.title LIKE ? OR j.description LIKE ? OR co.companyName LIKE ? OR c.name LIKE ?)';
            $searchTerm = '%' . $search . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($createdAfter !== null && $createdAfter !== '') {
            $clauses[] = 'j.createdAt >= ?';
            $params[] = $createdAfter;
        }

        if ($createdBefore !== null && $createdBefore !== '') {
            $clauses[] = 'j.createdAt <= ?';
            $params[] = $createdBefore;
        }

        if (!empty($clauses)) {
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= ' ORDER BY j.createdAt DESC';

        if ($limit !== null) {
            $sql .= ' LIMIT ? OFFSET ?';
            $params[] = intval($limit);
            $params[] = intval($offset);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getJobById(int $jobId): ?array
    {
        $sql = "SELECT j.*, c.name AS categoryName, co.companyName, co.logo AS companyLogo
                FROM jobs j
                LEFT JOIN categories c ON c.id = j.categoryId
                LEFT JOIN companies co ON co.id = j.companyId
                WHERE j.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([intval($jobId)]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getApplicationsForJob(int $jobId): array
    {
        try {
            $stmt = $this->pdo->prepare(
                'SELECT a.id, a.jobId, a.userId, a.fullName, a.email, a.phone, a.cv, a.coverLetter, a.status, a.appliedAt, a.updatedAt, u.username AS applicantUsername'
                . ' FROM applications a'
                . ' LEFT JOIN users u ON u.id = a.userId'
                . ' WHERE a.jobId = ?'
                . ' ORDER BY a.appliedAt DESC'
            );
            $stmt->execute([intval($jobId)]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Error fetching applications for job: ' . $e->getMessage());
        }
    }

    public function getApplicationById(int $applicationId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM applications WHERE id = ?');
        $stmt->execute([intval($applicationId)]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateApplicationStatus(int $applicationId, string $status): bool
    {
        $allowedStatuses = ['Applied', 'Shortlisted', 'Rejected', 'Accepted'];
        if (!in_array($status, $allowedStatuses, true)) {
            throw new Exception('Invalid application status');
        }

        $stmt = $this->pdo->prepare('UPDATE applications SET status = ?, updatedAt = CURRENT_TIMESTAMP WHERE id = ?');
        return $stmt->execute([$status, intval($applicationId)]);
    }

    public function createJob(string $title, string $description, int $categoryId, string $salary, ?int $companyId = null, string $location = '', int $postedBy = 0, string $jobType = '', string $closingDate = ''): string
    {
        $fields = ['title', 'description', 'salary', 'categoryId', 'companyId', 'location', 'jobType', 'closingDate', 'createdAt'];
        $values = [$title, $description, $salary, $categoryId, $companyId, $location, $jobType, $closingDate, date('Y-m-d H:i:s')];

        if ($this->hasColumn('jobs', 'postedBy')) {
            $fields[] = 'postedBy';
            $values[] = intval($postedBy);
        }

        if ($this->hasColumn('jobs', 'isArchived')) {
            $fields[] = 'isArchived';
            $values[] = 0;
        }

        $fieldList = implode(', ', $fields);
        $placeholderList = implode(', ', array_fill(0, count($fields), '?'));

        $sql = "INSERT INTO jobs ($fieldList) VALUES ($placeholderList)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);

        return $this->pdo->lastInsertId();
    }

    public function updateJob(int $jobId, string $title, string $description, int $categoryId, string $salary, int $companyId, string $location, string $jobType, string $closingDate): bool
    {
        $fields = ['title = ?', 'description = ?', 'salary = ?', 'categoryId = ?', 'companyId = ?', 'location = ?', 'jobType = ?', 'closingDate = ?'];
        $params = [$title, $description, $salary, $categoryId, $companyId, $location, $jobType, $closingDate, intval($jobId)];

        $sql = 'UPDATE jobs SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function deleteJob(int $jobId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM jobs WHERE id = ?');
        return $stmt->execute([intval($jobId)]);
    }

    public function setJobArchived(int $jobId, bool $archived = true): bool
    {
        if (!$this->hasColumn('jobs', 'isArchived')) {
            throw new Exception('The jobs table does not support archiving.');
        }

        $stmt = $this->pdo->prepare('UPDATE jobs SET isArchived = ? WHERE id = ?');
        return $stmt->execute([intval($archived ? 1 : 0), intval($jobId)]);
    }

    public function searchJobs(string $query, int $limit = 50): array
    {
        $query = trim($query);
        $sql = "SELECT j.*, c.name AS categoryName, co.companyName, co.logo AS companyLogo
                FROM jobs j
                LEFT JOIN categories c ON c.id = j.categoryId
                LEFT JOIN companies co ON co.id = j.companyId
                WHERE (j.title LIKE ? OR j.description LIKE ? OR co.companyName LIKE ? OR c.name LIKE ?)";

        $params = ["%$query%", "%$query%", "%$query%", "%$query%"];

        if ($this->hasColumn('jobs', 'isArchived')) {
            $sql .= ' AND j.isArchived = 0';
        }

        $sql .= ' ORDER BY j.createdAt DESC LIMIT ?';
        $params[] = intval($limit);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getJobsByCategory(int $categoryId): array
    {
        return $this->getAllJobs($categoryId, null, 0, false);
    }

    public function getJobRequirements(int $jobId): array
    {
        // Prefer dedicated job_requirements table when available and valid.
        try {
            if (function_exists('tableExists') && tableExists($this->pdo, 'job_requirements')) {
                // Ensure the expected column exists to avoid SQL errors on mismatched schemas
                if ($this->hasColumn('job_requirements', 'requirement')) {
                    $stmt = $this->pdo->prepare('SELECT requirement FROM job_requirements WHERE jobId = ? ORDER BY id ASC');
                    $stmt->execute([intval($jobId)]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    return array_map(fn($row) => $row['requirement'], $rows);
                }
            }
        } catch (PDOException $e) {
            // If the dedicated table/query fails (missing column, permission, etc.), fall back below.
        } catch (Exception $e) {
            // Any other issues - continue to fallback behavior.
        }

        // Fallback: try to read skills/requirements directly from the jobs row
        $job = $this->getJobById($jobId);
        if (!$job) {
            return [];
        }

        if (!empty($job['skills'])) {
            return array_filter(array_map('trim', explode(',', $job['skills'])));
        }

        if (!empty($job['requirements'])) {
            return array_filter(array_map('trim', explode(',', $job['requirements'])));
        }

        return [];
    }
}
