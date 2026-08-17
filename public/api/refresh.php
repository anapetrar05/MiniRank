<?php

declare(strict_types=1);

require_once __DIR__ . '/../../src/bootstrap.php';

// Refresh is a mutation, so only accept POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed. Use POST.']);
    exit;
}

// Must be logged in.
if (Auth::user() === null) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in.']);
    exit;
}

// AJAX sends the CSRF token in a header instead of a form field.
$sentToken = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
if (!Csrf::verify($sentToken)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid CSRF token.']);
    exit;
}

$repo = new KeywordRepository(Database::connection());
$service = new RankingService();

header('Content-Type: application/json');
echo json_encode(['keywords' => $service->refreshToday($repo)]);