<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/usercontroller.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';

final class JobSiteComprehensiveTestCase extends TestBaseCase
{
    private PDO $pdo;
    /** @var UserController */
    private $userController;
    /** @var JobController */
    private $jobController;

    protected function setUp(): void
    {
        $this->pdo = $this->createInMemoryPdo();
        $this->createUserSchema($this->pdo);
        $this->createJobSchema($this->pdo);
        $this->createEnquirySchema($this->pdo);
        $this->userController = new UserController($this->pdo);
        $this->jobController = new JobController($this->pdo);
    }

    /**
     * TEST CASE 1: User Registration and Authentication
     * Tests the user registration process and login authentication
     */
    public function testUserRegistrationAndAuthenticationFlow(): void
    {
        // Register a new user
        $userId = (int)$this->userController->createUser('john_doe', 'john@example.com', 'SecurePass123', 0);
        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);

        // Test authentication with correct credentials
        $user = $this->userController->authenticate('john_doe', 'SecurePass123');
        $this->assertNotNull($user);
        $this->assertSame('john_doe', $user['username']);
        $this->assertSame('john@example.com', $user['email']);
        $this->assertSame(0, (int)$user['role']);

        // Test authentication with incorrect password
        $invalidAuth = $this->userController->authenticate('john_doe', 'WrongPassword');
        $this->assertNull($invalidAuth);

        // Test authentication with non-existent user
        $noUser = $this->userController->authenticate('nonexistent', 'password');
        $this->assertNull($noUser);
    }

    /**
     * TEST CASE 2: Job Creation and Retrieval
     * Tests creating jobs and retrieving job details
     */
    public function testJobCreationAndRetrieval(): void
    {
        // Create a category first
        $this->pdo->exec("INSERT INTO categories (name) VALUES ('Information Technology')");
        $categoryId = (int)$this->pdo->lastInsertId();

        // Create a company
        $this->pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('Tech Corp', 'logo.png')");
        $companyId = (int)$this->pdo->lastInsertId();

        // Create a job with correct method signature
        $jobId = $this->jobController->createJob(
            'Senior PHP Developer',
            'Looking for experienced PHP developer',
            $categoryId,
            '50000-70000',
            $companyId,
            'Kathmandu',
            0,
            'Full-time',
            '2026-12-31'
        );
        $this->assertIsString($jobId);
        $this->assertGreaterThan(0, (int)$jobId);

        // Retrieve the job
        $job = $this->jobController->getJobById((int)$jobId);
        $this->assertNotNull($job);
        $this->assertSame('Senior PHP Developer', $job['title']);
        $this->assertSame('Full-time', $job['jobType']);
        $this->assertSame('Kathmandu', $job['location']);
    }

    /**
     * TEST CASE 3: Category Management
     * Tests adding, retrieving, and listing categories
     */
    public function testCategoryManagement(): void
    {
        // Insert multiple categories
        $categories = ['IT', 'Sales & Marketing', 'Human Resources', 'Finance'];
        $categoryIds = [];

        foreach ($categories as $catName) {
            $this->pdo->exec("INSERT INTO categories (name) VALUES ('" . addslashes($catName) . "')");
            $categoryIds[] = (int)$this->pdo->lastInsertId();
        }

        // Retrieve all categories (only the ones we just inserted)
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id IN (" . implode(',', $categoryIds) . ") ORDER BY name");
        $stmt->execute();
        $allCategories = $stmt->fetchAll();

        $this->assertCount(4, $allCategories);
        $this->assertSame('Finance', $allCategories[0]['name']);
        $this->assertSame('Human Resources', $allCategories[1]['name']);
        $this->assertSame('IT', $allCategories[2]['name']);
    }

    /**
     * TEST CASE 4: Job Filtering by Category
     * Tests retrieving jobs filtered by category
     */
    public function testJobFilteringByCategory(): void
    {
        // Create categories
        $this->pdo->exec("INSERT INTO categories (name) VALUES ('IT')");
        $itCategoryId = (int)$this->pdo->lastInsertId();
        
        $this->pdo->exec("INSERT INTO categories (name) VALUES ('Sales')");
        $salesCategoryId = (int)$this->pdo->lastInsertId();

        // Create a company
        $this->pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('Company A', 'logo.png')");
        $companyId = (int)$this->pdo->lastInsertId();

        // Create jobs in different categories
        for ($i = 0; $i < 3; $i++) {
            $this->jobController->createJob(
                "IT Job $i",
                'IT Description',
                $itCategoryId,
                '50000',
                $companyId,
                'Kathmandu',
                0,
                'Full-time',
                '2026-12-31'
            );
        }

        for ($i = 0; $i < 2; $i++) {
            $this->jobController->createJob(
                "Sales Job $i",
                'Sales Description',
                $salesCategoryId,
                '40000',
                $companyId,
                'Pokhara',
                0,
                'Part-time',
                '2026-12-31'
            );
        }

        // Get jobs by IT category
        $itJobs = $this->jobController->getJobsByCategory($itCategoryId);
        $this->assertCount(3, $itJobs);

        // Get jobs by Sales category
        $salesJobs = $this->jobController->getJobsByCategory($salesCategoryId);
        $this->assertCount(2, $salesJobs);
    }

    /**
     * TEST CASE 5: Contact Enquiry Creation
     * Tests creating and retrieving contact enquiries
     */
    public function testContactEnquiryCreation(): void
    {
        // Insert a contact enquiry
        $enquiry = [
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'subject' => 'Job Inquiry',
            'message' => 'I have a question about your services',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $stmt = $this->pdo->prepare(
            "INSERT INTO enquiries (name, email, subject, message, created_at) 
             VALUES (:name, :email, :subject, :message, :created_at)"
        );
        $stmt->execute($enquiry);
        $enquiryId = (int)$this->pdo->lastInsertId();

        // Retrieve the enquiry
        $stmt = $this->pdo->prepare("SELECT * FROM enquiries WHERE id = :id");
        $stmt->execute([':id' => $enquiryId]);
        $retrievedEnquiry = $stmt->fetch();

        $this->assertNotNull($retrievedEnquiry);
        $this->assertSame('Alice Smith', $retrievedEnquiry['name']);
        $this->assertSame('alice@example.com', $retrievedEnquiry['email']);
        $this->assertSame('Job Inquiry', $retrievedEnquiry['subject']);
    }

    /**
     * TEST CASE 6: User Role-Based Access Control
     * Tests that users have correct roles and permissions
     */
    public function testUserRoleBasedAccessControl(): void
    {
        // Create users with different roles
        $jobseekerId = $this->userController->createUser('jobseeker1', 'js1@example.com', 'pass123', 0);
        $employerId = $this->userController->createUser('employer1', 'emp1@example.com', 'pass123', 1);
        $adminId = $this->userController->createUser('admin1', 'admin1@example.com', 'pass123', 2);

        // Verify roles
        $jobseeker = $this->userController->getUserById($jobseekerId);
        $employer = $this->userController->getUserById($employerId);
        $admin = $this->userController->getUserById($adminId);

        $this->assertSame(0, (int)$jobseeker['role']);
        $this->assertSame(1, (int)$employer['role']);
        $this->assertSame(2, (int)$admin['role']);
    }

    /**
     * TEST CASE 7: Job Archive and Delete Operations
     * Tests archiving and deleting jobs
     */
    public function testJobArchiveAndDelete(): void
    {
        // Setup
        $this->pdo->exec("INSERT INTO categories (name) VALUES ('IT')");
        $categoryId = (int)$this->pdo->lastInsertId();
        $this->pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('Company A', 'logo.png')");
        $companyId = (int)$this->pdo->lastInsertId();

        // Create a job
        $jobId = (int)$this->jobController->createJob(
            'Test Job',
            'Test Description',
            $categoryId,
            '50000',
            $companyId,
            'Kathmandu',
            0,
            'Full-time',
            '2026-12-31'
        );

        // Archive the job
        $this->jobController->setJobArchived($jobId, true);
        $job = $this->jobController->getJobById($jobId);
        $this->assertSame(1, (int)$job['isArchived']);

        // Delete the job
        $this->jobController->deleteJob($jobId);
        $deletedJob = $this->jobController->getJobById($jobId);
        $this->assertNull($deletedJob);
    }

    /**
     * TEST CASE 8: Search Jobs by Query
     * Tests searching for jobs using keywords
     */
    public function testJobSearchByQuery(): void
    {
        // Setup
        $this->pdo->exec("INSERT INTO categories (name) VALUES ('IT')");
        $categoryId = (int)$this->pdo->lastInsertId();
        $this->pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('Company A', 'logo.png')");
        $companyId = (int)$this->pdo->lastInsertId();

        // Create multiple jobs
        $jobs = [
            'Senior PHP Developer',
            'Junior Laravel Developer',
            'Marketing Manager',
            'Sales Executive'
        ];

        foreach ($jobs as $title) {
            $this->jobController->createJob(
                $title,
                'Job Description',
                $categoryId,
                '50000',
                $companyId,
                'Kathmandu',
                0,
                'Full-time',
                '2026-12-31'
            );
        }

        // Search for PHP jobs
        $phpJobs = $this->jobController->searchJobs('PHP');
        $this->assertGreaterThanOrEqual(1, count($phpJobs));

        // Search for Developer jobs
        $devJobs = $this->jobController->searchJobs('Developer');
        $this->assertGreaterThanOrEqual(2, count($devJobs));
    }

    /**
     * TEST CASE 9: User Profile Update
     * Tests updating user profile information
     */
    public function testUserProfileUpdate(): void
    {
        // Create a user
        $userId = (int)$this->userController->createUser('testuser', 'test@example.com', 'password123', 0);
        $user = $this->userController->getUserById($userId);

        $this->assertSame('testuser', $user['username']);
        $this->assertSame('test@example.com', $user['email']);

        // Update user email
        $this->userController->updateUser($userId, 'newemail@example.com', 0);

        // Update user password
        $this->userController->updatePassword($userId, 'newpassword456');

        // Verify update
        $updatedUser = $this->userController->getUserById($userId);
        $this->assertSame('newemail@example.com', $updatedUser['email']);

        // Test authentication with new password
        $authResult = $this->userController->authenticate('testuser', 'newpassword456');
        $this->assertNotNull($authResult);
    }

    /**
     * TEST CASE 10: Job Listing with Pagination
     * Tests retrieving all jobs with proper pagination support
     */
    public function testJobListingWithPagination(): void
    {
        // Setup
        $this->pdo->exec("INSERT INTO categories (name) VALUES ('IT')");
        $categoryId = (int)$this->pdo->lastInsertId();
        $this->pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('Company A', 'logo.png')");
        $companyId = (int)$this->pdo->lastInsertId();

        // Create 25 jobs
        for ($i = 1; $i <= 25; $i++) {
            $this->jobController->createJob(
                "Job Position $i",
                "Description for job $i",
                $categoryId,
                '50000',
                $companyId,
                'Kathmandu',
                0,
                'Full-time',
                '2026-12-31'
            );
        }

        // Get all jobs
        $allJobs = $this->jobController->getAllJobs();
        $this->assertCount(25, $allJobs);

        // Get paginated results (simulating 10 per page)
        $pageSize = 10;
        $page1Jobs = array_slice($allJobs, 0, $pageSize);
        $page2Jobs = array_slice($allJobs, $pageSize, $pageSize);
        $page3Jobs = array_slice($allJobs, $pageSize * 2, $pageSize);

        $this->assertCount(10, $page1Jobs);
        $this->assertCount(10, $page2Jobs);
        $this->assertCount(5, $page3Jobs);
    }

    protected function createJobSchema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE companies (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                companyName TEXT NOT NULL,
                logo TEXT
            )'
        );

        $pdo->exec(
            'CREATE TABLE jobs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                description TEXT NOT NULL,
                salary TEXT,
                categoryId INTEGER,
                companyId INTEGER,
                location TEXT,
                jobType TEXT,
                closingDate TEXT,
                isArchived INTEGER DEFAULT 0,
                createdAt TEXT,
                FOREIGN KEY(categoryId) REFERENCES categories(id),
                FOREIGN KEY(companyId) REFERENCES companies(id)
            )'
        );
    }

    /**
     * Helper method to create enquiry schema
     */
    protected function createEnquirySchema(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS enquiries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                subject TEXT,
                message TEXT NOT NULL,
                status TEXT DEFAULT "new",
                created_at TEXT NOT NULL
            )'
        );
    }
}
