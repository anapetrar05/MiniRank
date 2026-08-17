<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

/**
 * Data access for user accounts.
 */
final class UserRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** User by email, or null when not found. */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, email, password_hash, created_at FROM users WHERE email = :email'
        );
        $stmt->execute(['email' => $email]);

        $row = $stmt->fetch();

        return $row === false ? null : $row;
    }

    /** Insert a user (password already hashed), returns the new id. */
    public function create(string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)'
        );
        $stmt->execute(['email' => $email, 'password_hash' => $passwordHash]);

        return (int) $this->db->lastInsertId();
    }
}