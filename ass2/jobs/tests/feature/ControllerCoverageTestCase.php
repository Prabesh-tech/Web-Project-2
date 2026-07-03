<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/DbConnection.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';
require_once __DIR__ . '/../../includes/usercontroller.php';

final class ControllerCoverageTestCase extends TestBaseCase
{
    public function testDbConnectionCreatesSqlitePdoAndTableExists(): void
    {
        putenv('DB_DRIVER=sqlite');
        putenv('DB_NAME=:memory:');

        require __DIR__ . '/../../includes/DbConnection.php';

        $this->assertInstanceOf(PDO::class, $pdo);
        $this->assertFalse(tableExists($pdo, 'missing_table'));

        $pdo->exec('CREATE TABLE example (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT)');
        $this->assertTrue(tableExists($pdo, 'example'));
    }

    public function testJobControllerSearchAndRequirementFallback(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);

        $stmt = $pdo->prepare('INSERT INTO jobs (title, description, salary, categoryId, companyId, location, jobType, closingDate, createdAt, skills, requirements) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            'Dev Role',
            'Development work',
            '60000',
            1,
            1,
            'Kathmandu',
            'Full-time',
            '2026-12-31',
            date('Y-m-d H:i:s'),
            'PHP,SQL',
            'Team player, problem solver',
        ]);

        $jobController = new JobController($pdo);
        $job = $jobController->getJobById(1);
        $this->assertNotNull($job);
        $this->assertSame('Dev Role', $job['title']);

        $results = $jobController->searchJobs('Development');
        $this->assertCount(1, $results);
        $this->assertSame('Dev Role', $results[0]['title']);

        $requirements = $jobController->getJobRequirements(1);
        $this->assertSame(['PHP', 'SQL'], $requirements);
    }

    public function testJobControllerApplicationsAndStatusUpdate(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);
        $this->createUserSchema($pdo);

        $pdo->exec("INSERT INTO jobs (title, description, salary, categoryId, companyId, location, jobType, closingDate, createdAt) VALUES ('App Role','App work','50000',1,1,'Kathmandu','Full-time','2026-12-31',datetime('now'))");
        $pdo->exec("INSERT INTO users (username, email, password, role, createdAt) VALUES ('user1','user1@example.com','secret',0,datetime('now'))");
        $pdo->exec("CREATE TABLE applications (id INTEGER PRIMARY KEY AUTOINCREMENT, jobId INTEGER, userId INTEGER, fullName TEXT, email TEXT, phone TEXT, cv TEXT, coverLetter TEXT, status TEXT, appliedAt TEXT)");
        $pdo->exec("INSERT INTO applications (jobId, userId, fullName, email, phone, cv, coverLetter, status, appliedAt) VALUES (1,1,'Applicant One','user1@example.com','9800000000','','','Applied',datetime('now'))");

        $jobController = new JobController($pdo);

        $applications = $jobController->getApplicationsForJob(1);
        $this->assertCount(1, $applications);
        $this->assertSame('Applicant One', $applications[0]['fullName']);

        $application = $jobController->getApplicationById((int)$applications[0]['id']);
        $this->assertSame('Applicant One', $application['fullName']);

        $updated = $jobController->updateApplicationStatus((int)$applications[0]['id'], 'Shortlisted');
        $this->assertTrue($updated);

        $updatedApplication = $jobController->getApplicationById((int)$applications[0]['id']);
        $this->assertSame('Shortlisted', $updatedApplication['status']);
    }

    public function testJobControllerGetAllJobsWithFiltersAndArchive(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);
        $pdo->exec('ALTER TABLE jobs ADD COLUMN isArchived INTEGER DEFAULT 0');

        $pdo->exec("INSERT INTO jobs (title, description, salary, categoryId, companyId, location, jobType, closingDate, createdAt, isArchived) VALUES ('Active Job','Active work','55000',1,1,'Kathmandu','Full-time','2026-12-31',datetime('now'),0)");
        $pdo->exec("INSERT INTO jobs (title, description, salary, categoryId, companyId, location, jobType, closingDate, createdAt, isArchived) VALUES ('Archived Job','Archived work','55000',1,1,'Kathmandu','Full-time','2026-12-31',datetime('now'),1)");

        $jobController = new JobController($pdo);

        $all = $jobController->getAllJobs(null, null, 0, false, null, null, null);
        $this->assertCount(1, $all);
        $this->assertSame('Active Job', $all[0]['title']);

        $allWithArchived = $jobController->getAllJobs(null, null, 0, true, null, null, null);
        $this->assertCount(2, $allWithArchived);

        $filtered = $jobController->getAllJobs(null, null, 0, true, 'Archived', null, null);
        $this->assertCount(1, $filtered);
    }

    public function testJobControllerCreateUpdateDeleteWithDateFilters(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);
        $pdo->exec('ALTER TABLE jobs ADD COLUMN isArchived INTEGER DEFAULT 0');

        $pdo->exec("INSERT INTO categories (name) VALUES ('IT')");
        $pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('T', 'logo.png')");

        $jobController = new JobController($pdo);
        $jobId = $jobController->createJob(
            'Filter Job',
            'Filter work',
            1,
            '50000',
            1,
            'Kathmandu',
            0,
            'Full-time',
            '2026-12-31'
        );

        $this->assertIsString($jobId);
        $this->assertGreaterThan(0, (int)$jobId);

        $updated = $jobController->updateJob((int)$jobId, 'Updated Filter Job', 'Updated work', 1, '55000', 1, 'Kathmandu', 'Full-time', '2026-12-31');
        $this->assertTrue($updated);

        $job = $jobController->getJobById((int)$jobId);
        $this->assertSame('Updated Filter Job', $job['title']);

        $after = date('Y-m-d H:i:s', strtotime('-1 day'));
        $before = date('Y-m-d H:i:s', strtotime('+1 day'));
        $filteredByDate = $jobController->getAllJobs(null, null, 0, true, null, $after, $before);
        $this->assertNotEmpty($filteredByDate);

        $deleted = $jobController->deleteJob((int)$jobId);
        $this->assertTrue($deleted);
    }

    public function testUserControllerGetAllUsersAndApplications(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, createdAt) VALUES (?, ?, ?, ?, datetime('now'))");
        $stmt->execute(['bob', 'bob@example.com', 'secret', 0]);
        $stmt->execute(['carol', 'carol@example.com', 'secret', 1]);
        $stmt->execute(['dave', 'dave@example.com', 'secret', 0]);

        $pdo->exec('CREATE TABLE jobs (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, description TEXT, salary TEXT, categoryId INTEGER, companyId INTEGER, location TEXT, jobType TEXT, closingDate TEXT, createdAt TEXT)');
        $stmt = $pdo->prepare('CREATE TABLE applications (id INTEGER PRIMARY KEY AUTOINCREMENT, jobId INTEGER, userId INTEGER, fullName TEXT, email TEXT, phone TEXT, cv TEXT, coverLetter TEXT, status TEXT, appliedAt TEXT)');
        $stmt->execute();
        $pdo->exec("INSERT INTO applications (jobId, userId, fullName, email, phone, cv, coverLetter, status, appliedAt) VALUES (1,1,'Bob','bob@example.com','9800000000','','','Applied',datetime('now'))");
        $pdo->exec("INSERT INTO applications (jobId, userId, fullName, email, phone, cv, coverLetter, status, appliedAt) VALUES (2,1,'Bob','bob@example.com','9800000000','','','Shortlisted',datetime('now'))");

        $userController = new UserController($pdo);
        $allUsers = $userController->getAllUsers(2, 0);
        $this->assertCount(2, $allUsers);

        $applications = $userController->getUserApplications(1);
        $this->assertCount(2, $applications);

        $applicationsFiltered = $userController->getUserApplications(1, 'Applied');
        $this->assertCount(1, $applicationsFiltered);
    }

    public function testUserControllerSearchAndCountFilters(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $userController = new UserController($pdo);
        $userController->createUser('anna', 'anna@example.com', 'pass123', 0);
        $userController->createUser('annab', 'annab@example.com', 'pass123', 0);
        $userController->createUser('john', 'john@example.com', 'pass123', 0);

        $results = $userController->searchUsers('anna', 10, 0);
        $this->assertCount(2, $results);

        $countSearch = $userController->getUserCount('anna');
        $this->assertSame(2, $countSearch);

        $allUsers = $userController->getAllUsers();
        $this->assertCount(3, $allUsers);
    }

    public function testUserControllerAuthenticateAndLookup(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $userController = new UserController($pdo);
        $userId = (int) $userController->createUser('encrypted', 'encrypt@example.com', 'securePass', 1);

        $userById = $userController->getUserById($userId);
        $this->assertSame('encrypt@example.com', $userById['email']);

        $auth = $userController->authenticate('encrypted', 'securePass');
        $this->assertNotNull($auth);
        $this->assertSame('encrypted', $auth['username']);

        $authEmail = $userController->authenticateWithEmail('encrypt@example.com', 'securePass');
        $this->assertNotNull($authEmail);
        $this->assertSame('encrypt@example.com', $authEmail['email']);

        $updatedEmail = $userController->updateUser($userId, 'updated@example.com', null);
        $this->assertTrue($updatedEmail);

        $adminCount = $userController->getAdminCount();
        $this->assertSame(1, $adminCount);
    }

    public function testUserControllerExceptionPaths(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);
        $userController = new UserController($pdo);
        $pdo->exec('DROP TABLE users');

        $methods = [
            fn() => $userController->getAllUsers(),
            fn() => $userController->getUserById(1),
            fn() => $userController->getUserByUsername('test'),
            fn() => $userController->getUserApplications(1),
            fn() => $userController->searchUsers('test'),
            fn() => $userController->getUserCount(),
            fn() => $userController->createUser('tester', 't@example.com', 'secret123'),
            fn() => $userController->updateUser(1, 't@example.com', 1),
            fn() => $userController->updatePassword(1, 'secret123'),
            fn() => $userController->deleteUser(1),
            fn() => $userController->getAdminCount(),
            fn() => $userController->authenticate('test', 'secret'),
            fn() => $userController->authenticateWithEmail('t@example.com', 'secret'),
        ];

        foreach ($methods as $method) {
            try {
                $method();
                $this->fail('Expected exception for broken user schema');
            } catch (Exception $e) {
                $this->assertStringContainsString('Error', $e->getMessage());
            }
        }
    }

    public function testJobControllerArchivedAndCategoryMethods(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);
        $pdo->exec('ALTER TABLE jobs ADD COLUMN isArchived INTEGER DEFAULT 0');

        $pdo->exec("INSERT INTO jobs (title, description, salary, categoryId, companyId, location, jobType, closingDate, createdAt, isArchived) VALUES ('Category Job','Work detail','40000',1,1,'Kathmandu','Full-time','2026-12-31',datetime('now'),0)");
        $jobController = new JobController($pdo);

        $archived = $jobController->setJobArchived(1, true);
        $this->assertTrue($archived);

        $job = $jobController->getJobById(1);
        $this->assertSame('1', (string) $job['isArchived']);

        $jobsByCategory = $jobController->getJobsByCategory(1);
        $this->assertCount(0, $jobsByCategory);

        $unarchived = $jobController->setJobArchived(1, false);
        $this->assertTrue($unarchived);

        $jobsByCategory = $jobController->getJobsByCategory(1);
        $this->assertCount(1, $jobsByCategory);
    }
}
