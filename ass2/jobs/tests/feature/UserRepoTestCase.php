<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/usercontroller.php';

final class UserRepoTestCase extends TestBaseCase
{
    public function testUserRepositoryWorkflowCreatesAndRetrievesUsers(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $userController = new UserController($pdo);
        $userId = $userController->createUser('bob', 'bob@example.com', 'secret123', 0);

        $this->assertGreaterThan(0, (int) $userId);

        $user = $userController->getUserById((int) $userId);
        $this->assertSame('bob', $user['username']);
        $this->assertSame('bob@example.com', $user['email']);
        $this->assertSame(0, $user['role']);
    }
}
