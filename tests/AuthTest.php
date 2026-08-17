<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    use CreatesInMemoryRepo;

    public function testCreateAndFindByEmailRoundTrip(): void
    {
        $pdo = $this->makePdo();
        $this->applySchema($pdo);
        $users = new UserRepository($pdo);

        $id = $users->create('test@example.com', password_hash('secret-pass', PASSWORD_DEFAULT));
        $row = $users->findByEmail('test@example.com');

        $this->assertNotNull($row);
        $this->assertSame((int) $id, (int) $row['id']);
        $this->assertSame('test@example.com', $row['email']);
    }

    public function testFindByEmailReturnsNullForUnknownEmail(): void
    {
        $pdo = $this->makePdo();
        $this->applySchema($pdo);
        $users = new UserRepository($pdo);

        $this->assertNull($users->findByEmail('nobody@example.com'));
    }

    public function testPasswordHashVerifiesOnlyWithCorrectPassword(): void
    {
        $hash = password_hash('correct horse battery staple', PASSWORD_DEFAULT);

        $this->assertTrue(password_verify('correct horse battery staple', $hash));
        $this->assertFalse(password_verify('wrong password', $hash));
    }

    public function testEnsureDemoUserCreatesOnceAndIsIdempotent(): void
    {
        $pdo = $this->makePdo();
        $this->applySchema($pdo);
        $seeder = new Seeder($pdo, new KeywordRepository($pdo));

        $first = $seeder->ensureDemoUser();
        $second = $seeder->ensureDemoUser();

        $this->assertSame(['created' => 1], $first);
        $this->assertSame(['created' => 0], $second);

        $user = (new UserRepository($pdo))->findByEmail('demo@example.com');
        $this->assertNotNull($user);
        $this->assertTrue(password_verify('demo1234', $user['password_hash']));
    }

    public function testCsrfVerifyAcceptsMatchingToken(): void
    {
        $token = Csrf::token();

        $this->assertTrue(Csrf::verify($token));
    }

    public function testCsrfVerifyRejectsWrongOrMissingToken(): void
    {
        $token = Csrf::token();

        $this->assertFalse(Csrf::verify('not-the-token'));
        $this->assertFalse(Csrf::verify(''));
        $this->assertFalse(Csrf::verify(strrev($token)));
    }
}