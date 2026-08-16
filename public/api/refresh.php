<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/KeywordRepository.php';
require_once __DIR__ . '/../../src/RankingService.php';

// Refresh is a mutation, so only accept POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

$repo = new KeywordRepository(Database::connection());
$service = new RankingService();

header('Content-Type: application/json');
echo json_encode(['keywords' => $service->refreshToday($repo)]);