<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/jobcontroller.php';
require_once __DIR__ . '/../../includes/usercontroller.php';

final class ControllersCombinedTest extends TestBaseCase
{
    public function testCreateAndGetJob(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createJobSchema($pdo);

        $jobController = new JobController($pdo);
        $id = $jobController->createJob('Dev', 'Work', 1, '1000', 1, 'City');

        $this->assertGreaterThan(0, (int)$id);
        $job = $jobController->getJobById((int)$id);
        $this->assertSame('Dev', $job['title']);
    }

    public function testCreateAndGetUser(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $userController = new UserController($pdo);
        $id = (int)$userController->createUser('tester', 't@example.com', 'secret123');

        $this->assertGreaterThan(0, $id);
        $user = $userController->getUserById($id);
        $this->assertSame('tester', $user['username']);
    }
}
