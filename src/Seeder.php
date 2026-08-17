<?php

declare(strict_types=1);

require_once __DIR__ . '/KeywordRepository.php';

/**
 * Creates the schema and seeds demo ranking data.
 * Lives in src/ (not scripts/) so the logic can be unit-tested.
 */
final class Seeder
{
    private PDO $pdo;
    private KeywordRepository $repo;

    public function __construct(PDO $pdo, KeywordRepository $repo)
    {
        $this->pdo = $pdo;
        $this->repo = $repo;
    }

    public function schemaFile(): string
    {
        return __DIR__ . '/../migrations/schema.sql';
    }

    /** Apply the schema (idempotent). */
    public function applySchema(): void
    {
        $this->pdo->exec(file_get_contents($this->schemaFile()));
    }

    /**
     * Seed demo keywords and ~30 days of daily positions (1-100).
     * Returns ['created' => int, 'positions' => int]; both 0 when the
     * database already contains keywords.
     */
    public function seed(): array
    {
        $this->applySchema();

        if (count($this->repo->all()) > 0) {
            return ['created' => 0, 'positions' => 0];
        }

        $demoKeywords = [
            'seo guide',
            'link building',
            'keyword research',
            'backlink checker',
            'site audit',
            'rank tracker',
        ];

        foreach ($demoKeywords as $keyword) {
            $this->repo->create($keyword);
        }

        $days = 30;
        $today = new DateTimeImmutable();
        $positions = 0;

        // One simulated position per keyword per day, between 1 and 100.
        foreach ($this->repo->all() as $keyword) {
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = $today->modify("-{$i} days")->format('Y-m-d');
                $this->repo->upsertPosition((int) $keyword['id'], $date, mt_rand(1, 100));
                $positions++;
            }
        }

        return ['created' => count($demoKeywords), 'positions' => $positions];
    }
}