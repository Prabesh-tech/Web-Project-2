<?php

declare(strict_types=1);

require_once __DIR__ . '/TestBaseCase.php';
require_once __DIR__ . '/../../includes/usercontroller.php';

final class AuthHelperTestCase extends TestBaseCase
{
    public function testUserAuthenticationSucceedsForValidCredentialsAndFailsOtherwise(): void
    {
        $pdo = $this->createInMemoryPdo();
        $this->createUserSchema($pdo);

        $userController = new UserController($pdo);
        $userController->createUser('alice', 'alice@example.com', 'secret123', 0);

        $authenticatedUser = $userController->authenticate('alice', 'secret123');
        $this->assertNotNull($authenticatedUser);
        $this->assertSame('alice', $authenticatedUser['username']);
        $this->assertSame('alice@example.com', $authenticatedUser['email']);

        $this->assertNull($userController->authenticate('alice', 'wrongpass'));
        $this->assertNull($userController->authenticate('unknown', 'secret123'));
    }
}
