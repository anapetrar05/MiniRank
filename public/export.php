<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/KeywordRepository.php';

$repo = new KeywordRepository(Database::connection());

$id = (int) ($_GET['id'] ?? 0);
$keyword = $repo->find($id);

if ($keyword === null) {
    http_response_code(404);
    echo 'Keyword not found.';
    exit;
}

$history = $repo->history($id);

// Safe filename: strip characters that could break the header (newlines, quotes).
$filename = preg_replace('/[^A-Za-z0-9._-]/', '-', $keyword['keyword']) ?: 'keyword';
$filename .= '-positions.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Date', 'Position']);
foreach ($history as $row) {
    fputcsv($out, [$row['date'], $row['position']]);
}
fclose($out);