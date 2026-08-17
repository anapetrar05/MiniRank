<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$pdo = Database::connection();
$seeder = new Seeder($pdo, new KeywordRepository($pdo));

$result = $seeder->seed();
$demoUser = $seeder->ensureDemoUser();

printf(
    "Seeded %d keywords with %d position rows (approx. %d days each).\n",
    $result['created'],
    $result['positions'],
    $result['positions'] / max(1, $result['created'])
);

if ($demoUser['created'] === 1) {
    echo "Demo account created: demo@example.com / demo1234\n";
} elseif ($result['created'] === 0 && $demoUser['created'] === 0) {
    echo "Database already seeded; nothing to do.\n";
}