<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';
require_once __DIR__ . '/../../includes/usercontroller.php';

final class CoverageBoosterTestCase extends TestBaseCase
{
    public function testJobControllerOperations(): void
    {
        $pdo = $this->createInMemoryPdo();
        // Create schema with some extra columns to exercise branches
        $pdo->exec('CREATE TABLE categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL)');
        $pdo->exec('CREATE TABLE companies (id INTEGER PRIMARY KEY AUTOINCREMENT, companyName TEXT NOT NULL, logo TEXT)');
        // include skills and requirements columns to exercise getJobRequirements fallback with data
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
                createdAt TEXT,
                skills TEXT,
                requirements TEXT,
                postedBy INTEGER,
                isArchived INTEGER DEFAULT 0
            )'
        );

        $pdo->exec("INSERT INTO categories (name) VALUES ('Dev')");
        $pdo->exec("INSERT INTO companies (companyName, logo) VALUES ('ACME', 'logo.png')");
        $pdo->exec('CREATE TABLE job_requirements (id INTEGER PRIMARY KEY AUTOINCREMENT, jobId INTEGER, requirement TEXT)');
        $pdo->exec("INSERT INTO job_requirements (jobId, requirement) VALUES (1, 'Requirement A')");

        $jobController = new JobController($pdo);

        // create job
        $id = $jobController->createJob('Dev', 'Work', 1, '1000', 1, 'City');
        $this->assertGreaterThan(0, (int)$id);

        $job = $jobController->getJobById((int)$id);
        $this->assertSame('Dev', $job['title']);

        // update job
        $updated = $jobController->updateJob((int)$id, 'Dev2', 'Work2', 1, '2000', 1, 'City', '', '');
        $this->assertTrue($updated);

        $all = $jobController->getAllJobs();
        $this->assertIsArray($all);
        $this->assertNotEmpty($all);

        // search
        $found = $jobController->searchJobs('Dev2');
        $this->assertIsArray($found);

        $jobsByCategory = $jobController->getJobsByCategory(1);
        $this->assertIsArray($jobsByCategory);

        $jobRequirements = $jobController->getJobRequirements((int)$id);
        $this->assertContains('Requirement A', $jobRequirements);

        // create applications and users to exercise getApplicationsForJob
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT NOT NULL, email TEXT NOT NULL, password TEXT NOT NULL, role INTEGER NOT NULL DEFAULT 0, createdAt TEXT)');
        $pdo->exec('CREATE TABLE applications (id INTEGER PRIMARY KEY AUTOINCREMENT, jobId INTEGER, userId INTEGER, fullName TEXT, email TEXT, phone TEXT, cv TEXT, coverLetter TEXT, status TEXT, appliedAt TEXT)');

        $pdo->exec("INSERT INTO users (username, email, password, role, createdAt) VALUES ('jdoe','j@example.com','pass',0,datetime('now'))");
        $pdo->exec("INSERT INTO applications (jobId, userId, fullName, email, phone, cv, coverLetter, status, appliedAt) VALUES (1,1,'John Doe','j@example.com','123','', '', 'Applied', datetime('now'))");

        $apps = $jobController->getApplicationsForJob((int)$id);
        $this->assertIsArray($apps);
        $this->assertCount(1, $apps);

        $applicationId = (int)$apps[0]['id'];
        $ok = $jobController->updateApplicationStatus($applicationId, 'Shortlisted');
        $this->assertTrue($ok);

        $updatedApp = $jobController->getApplicationById($applicationId);
        $this->assertSame('Shortlisted', $updatedApp['status']);

        $archived = $jobController->setJobArchived((int)$id, true);
        $this->assertTrue($archived);

        $unarchived = $jobController->setJobArchived((int)$id, false);
        $this->assertTrue($unarchived);

        $after = date('Y-m-d H:i:s', strtotime('-1 day'));
        $before = date('Y-m-d H:i:s', strtotime('+1 day'));
        $filtered = $jobController->getAllJobs(null, null, 0, true, 'Dev', $after, $before);
        $this->assertNotEmpty($filtered);

        $deleted = $jobController->deleteJob((int)$id);
        $this->assertTrue($deleted);
    }

    public function testUserControllerOperations(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $userController = new UserController($pdo);

        // create user
        $id = (int)$userController->createUser('tester', 't@example.com', 'secret123');
        $this->assertGreaterThan(0, $id);

        $user = $userController->getUserById($id);
        $this->assertSame('tester', $user['username']);

        // authenticate with email
        $auth = $userController->authenticateWithEmail('t@example.com', 'secret123');
        $this->assertNotNull($auth);

        // get count
        $count = $userController->getUserCount();
        $this->assertGreaterThanOrEqual(1, $count);

        // update password
        $changed = $userController->updatePassword($id, 'newsecret');
        $this->assertTrue($changed);

        // search users
        $found = $userController->searchUsers('test');
        $this->assertNotEmpty($found);

        // delete user
        $deleted = $userController->deleteUser($id);
        $this->assertTrue($deleted);
    }
}
