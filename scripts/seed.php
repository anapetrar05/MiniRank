<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/KeywordRepository.php';

$pdo = Database::connection();

// Apply schema (idempotent).
$pdo->exec(file_get_contents(__DIR__ . '/../migrations/schema.sql'));

$repo = new KeywordRepository($pdo);

// Only seed when the database is empty, so re-runs never duplicate data.
if (count($repo->all()) > 0) {
    echo "Database already seeded; nothing to do.\n";
    exit(0);
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
    $repo->create($keyword);
}

$days = 30;
$today = new DateTimeImmutable();
$rows = 0;

// One simulated position per keyword per day, between 1 and 100.
foreach ($repo->all() as $keyword) {
    for ($i = $days - 1; $i >= 0; $i--) {
        $date = $today->modify("-{$i} days")->format('Y-m-d');
        $repo->upsertPosition((int) $keyword['id'], $date, mt_rand(1, 100));
        $rows++;
    }
}

printf(
    "Seeded %d keywords with %d position rows (approx. %d days each).\n",
    count($demoKeywords),
    $rows,
    $days
);