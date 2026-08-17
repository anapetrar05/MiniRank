<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Seeder.php';

$pdo = Database::connection();
$seeder = new Seeder($pdo, new KeywordRepository($pdo));

$result = $seeder->seed();

if ($result['created'] === 0) {
    echo "Database already seeded; nothing to do.\n";
    exit(0);
}

printf(
    "Seeded %d keywords with %d position rows (approx. %d days each).\n",
    $result['created'],
    $result['positions'],
    $result['positions'] / $result['created']
);