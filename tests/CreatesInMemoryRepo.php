<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Creates an in-memory SQLite connection + repository, so tests never
 * touch the real data/minirank.db.
 */
trait CreatesInMemoryRepo
{
    private function makeRepo(bool $seeded = false): KeywordRepository
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');

        $repo = new KeywordRepository($pdo);

        if ($seeded) {
            (new Seeder($pdo, $repo))->seed();
        }

        return $repo;
    }

    private function makePdo(): PDO
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');

        return $pdo;
    }

    private function applySchema(PDO $pdo): void
    {
        (new Seeder($pdo, new KeywordRepository($pdo)))->applySchema();
    }
}